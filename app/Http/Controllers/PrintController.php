<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\PrinterM;
use Exception;
use Illuminate\Http\Request;
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use PDF;

class PrintController extends Controller
{

    public function index()
    {
        $printers = PrinterM::all(); 

        return view('panel.admin', [
            'printers' => $printers,
            'videoUrl' => config('panel.video_url')
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'ip' => 'required',
        ]);

        PrinterM::create($request->only('name', 'ip'));

        return redirect()->route('adminPanel')->with('success', 'Impressora cadastrada com sucesso!');

    }

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
    //     $printer = PrinterM::latest()->first(); // ou busque pelo ID

    //     if (!$printer) {
    //         return response()->json(['error' => 'Nenhuma impressora cadastrada'], 400);
    //     }

    //     $content = "";
    //     $content .= "       TICKET DE ATENDIMENTO\n";
    //     $content .= "==============================\n";
    //     $content .= "Número: {$ticket->id}\n";
    //     $content .= "Tipo: " . strtoupper($ticket->type) . "\n";
    //     $content .= "Data: " . now()->format('d/m/Y H:i') . "\n";
    //     $content .= "Fila: " . strtoupper($ticket->stage) . "\n";
    //     $content .= "==============================\n";
    //     $content .= "     AGUARDE SER CHAMADO\n";

    //     $tmpFile = tempnam(sys_get_temp_dir(), 'ticket_');
    //     file_put_contents($tmpFile, $content);

    //     try {
    //         // imprime via IP
    //         $connector = new NetworkPrintConnector($printer->ip, 9100);
    //         $printerDevice = new Printer($connector);
    //         $printerDevice->text($content);
    //         $printerDevice->cut();
    //         $printerDevice->close();

    //         unlink($tmpFile);

    //         return response()->json(['message' => 'Impressão enviada com sucesso']);
    //     } catch (\Exception $e) {
    //         unlink($tmpFile);
    //         \Log::error("Erro ao imprimir: " . $e->getMessage());
    //         return response()->json(['error' => $e->getMessage()], 500);
    //     }
    // }


    

}
