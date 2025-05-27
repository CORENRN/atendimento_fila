<?php

namespace App\Http\Controllers;

use App\Events\TicketUpdated;
use App\Models\Ticket;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function home()
    {
        return view('home');
    }

public function queue($stage)
    {
        $status = $stage === 'triagem' ? 'triagem' : 'atendimento';

        $attendantId = auth()->id() ?? 1; // ID do atendente logado (ou fallback)

        // Busca os tickets na fila para o estágio atual (aguardando ou em andamento)
        $tickets = Ticket::where('stage', $stage)
            ->whereIn('status', ['aguardando', $status])
            // Ordena colocando chamados sem chamado primeiro e depois pela data chamada
            ->orderByRaw('called_at IS NULL, called_at ASC')
            ->orderBy('created_at')
            ->get();

        // Enum services (de acordo com sua migration)
        $services = [
            'financeiro' => 'Financeiro',
            'documentacao' => 'Documentação',
            'informacoes' => 'Informações',
            'cadastro' => 'Cadastro',
            'suporte' => 'Suporte',
        ];

        // Busca se este atendente já chamou algum ticket que está ativo no stage
        $calledTicket = Ticket::where('stage', $stage)
            ->where('attendant_id', $attendantId)
            ->whereIn('status', [$status])
            ->whereNotNull('called_at')
            ->whereNull('finished_at')
            ->first();

        $called_id = $calledTicket ? $calledTicket->id : null;

        $statusCounts = Ticket::where('stage', $stage)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('queue', compact('tickets', 'stage', 'services', 'called_id', 'statusCounts'));
    }

    public function callNext($stage)
    {
        $attendantId = auth()->id() ?? 1; // Usa o atendente logado ou 1 como fallback

        // Verifica se o atendente já está com algum ticket chamado e não finalizado nesse stage
        $existingCall = Ticket::where('stage', $stage)
            ->where('attendant_id', $attendantId)
            ->whereIn('status', ['triagem', 'atendimento'])
            ->whereNotNull('called_at')
            ->whereNull('finished_at')
            ->first();

        if ($existingCall) {
            return redirect()->back()->with('error', 'Você já está atendendo um ticket. Finalize-o antes de chamar outro.');
        }

        // Pega o próximo ticket aguardando no stage
        $ticket = Ticket::where('stage', $stage)
            ->where('status', 'aguardando')
            ->orderBy('created_at')
            ->first();

        if ($ticket) {
            $data = [
                'status' => $stage === 'triagem' ? 'triagem' : 'atendimento',
                'attendant_id' => $attendantId,
            ];

            // Só seta o called_at se ainda não tiver sido chamado antes
            if (is_null($ticket->called_at)) {
                $data['called_at'] = now();
            }

            $ticket->update($data);

            return redirect()->back()->with('success', 'Ticket chamado com sucesso.');
        }

        return redirect()->back()->with('info', 'Não há tickets aguardando na fila.');
    }



    public function finish(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'status' => 'finalizado',
            'finished_at' => now(),
        ]);

        event(new TicketUpdated($ticket));

        return redirect()->back();
    }

    public function cancel (Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        $ticket->update([
            'status' => 'cancelado',
            'finished_at' => now(),
        ]);

        event(new TicketUpdated($ticket));

        return redirect()->back();
    }

    public function advance(Request $request, $id)
    {

        $ticket = Ticket::findOrFail($id);

        $request->validate([
            'service' => 'required|in:financeiro,documentacao,informacoes,cadastro,suporte',
        ]);

        if ($ticket->stage === 'triagem') {
            $ticket->update([
                'stage' => 'atendimento',
                'status' => 'aguardando',
                'finished_at' => null,
                'attendant_id' => null,
                'service' => $request->service,
            ]);

            event(new TicketUpdated($ticket));
        }

        return redirect()->back();
    }



    public function takeTicket(Request $request)
    {
        $request->validate([
            'type' => 'required|in:regular,preferencial',
        ]);

        $ticket = Ticket::create([
            'type' => $request->type,
            'stage' => 'triagem',
            'status' => 'aguardando',
        ]);

        event(new TicketUpdated($ticket));

        return redirect()->route('ticket.show', ['id' => $ticket->id]);
    }

    public function showTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('ticket_show', compact('ticket'));
    }
}
