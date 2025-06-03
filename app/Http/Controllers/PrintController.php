<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Exception;
use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use PDF;

class PrintController extends Controller
{
public function printTicket($id)
{
    $ticket = Ticket::findOrFail($id);

    //estrutura do ticket
    $content = "";
    $content .= "       TICKET DE ATENDIMENTO\n";
    $content .= "==============================\n";
    $content .= "Número: {$ticket->id}\n";
    $content .= "Tipo: " . strtoupper($ticket->type) . "\n";
    $content .= "Data: " . now()->format('d/m/Y H:i') . "\n";
    $content .= "Fila: " . strtoupper($ticket->stage) . "\n";
    $content .= "==============================\n";
    $content .= "     AGUARDE SER CHAMADO\n";

    // cria arquivo temporário com o conteúdo do ticket
    $tmpFile = tempnam(sys_get_temp_dir(), 'ticket_');
    file_put_contents($tmpFile, $content);
    //pega nome cadastrado no smbclient
    $printerName = 'DarumaDR700';

    try {
        exec("lp -d {$printerName} " . escapeshellarg($tmpFile), $output, $returnVar);

        unlink($tmpFile);

        if ($returnVar !== 0) {
            throw new \Exception("Erro ao enviar impressão. Código de retorno: {$returnVar}");
        }

        \Log::info("Ticket {$ticket->id} enviado para impressão via lp.");

        return response()->json(['message' => 'Impressão enviada com sucesso']);
    } catch (\Exception $e) {
        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }
        \Log::error("Erro na impressão do ticket {$ticket->id}: " . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    // public function printTicket($id)
    // {
    //     $ticket = Ticket::findOrFail($id);

    //     $width_mm = 50;
    //     $height_mm = 70;
    //     $width_pt = $width_mm * 72 / 25.4;
    //     $height_pt = $height_mm * 72 / 25.4;

    //     $pdf = PDF::loadView('tickets.print', compact('ticket'))
    //         ->setPaper([0, 0, $width_pt, $height_pt]);

    //     $tmpFile = tempnam(sys_get_temp_dir(), 'ticket_') . '.pdf';
    //     $pdf->save($tmpFile);

    //     $printerName = 'DarumaDR700';

    //     try {
    //         exec("lp -d {$printerName} " . escapeshellarg($tmpFile), $output, $returnVar);

    //         unlink($tmpFile);

    //         if ($returnVar !== 0) {
    //             throw new \Exception("Erro ao enviar impressão. Código de retorno: {$returnVar}");
    //         }

    //         \Log::info("Ticket {$ticket->id} enviado para impressão via lp (PDF).");

    //         return response()->json(['message' => 'Impressão enviada com sucesso']);
    //     } catch (\Exception $e) {
    //         if (file_exists($tmpFile)) {
    //             unlink($tmpFile);
    //         }
    //         \Log::error("Erro na impressão do ticket {$ticket->id}: " . $e->getMessage());
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }

}
