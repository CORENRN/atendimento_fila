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

        $attendantId = auth()->id();

        $tickets = Ticket::where('stage', $stage)
            ->whereIn('status', ['aguardando', $status])
            ->orderByRaw('called_at IS NULL, called_at ASC')
            ->orderBy('created_at')
            ->get();

        $services = [
            'financeiro' => 'Financeiro',
            'documentacao' => 'Documentação',
            'informacoes' => 'Informações',
            'cadastro' => 'Cadastro',
            'suporte' => 'Suporte',
        ];

        // Ticket que o atendente logado está atendendo (se existir)
        $calledTicket = Ticket::where('stage', $stage)
            ->where('attendant_id', $attendantId)
            ->whereIn('status', [$status])
            ->whereNotNull('called_at')
            ->whereNull('finished_at')
            ->first();

        $called_id = $calledTicket?->id;

        $statusCounts = Ticket::where('stage', $stage)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('queue', compact('tickets', 'stage', 'services', 'called_id', 'statusCounts'));
    }

    public function callNext($stage)
    {
        $attendantId = auth()->id();

        $existingCall = Ticket::where('stage', $stage)
            ->where('attendant_id', $attendantId)
            ->whereIn('status', ['triagem', 'atendimento'])
            ->whereNotNull('called_at')
            ->whereNull('finished_at')
            ->first();

        if ($existingCall) {
            return back()->with('error', 'Você já está atendendo um ticket. Finalize-o antes de chamar outro.');
        }

        $ticket = Ticket::where('stage', $stage)
            ->where('status', 'aguardando')
            ->orderBy('created_at')
            ->first();

        if ($ticket) {
            $data = [
                'status' => $stage === 'triagem' ? 'triagem' : 'atendimento',
                'attendant_id' => $attendantId,
                'called_at' => $ticket->called_at ?? now(),
            ];

            $ticket->update($data);

            event(new TicketUpdated($ticket));

            return back()->with('success', 'Ticket chamado com sucesso.');
        }

        return back()->with('info', 'Não há tickets aguardando na fila.');
    }

    public function finish(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // ✅ Verifica se é o atendente correto
        if ($ticket->attendant_id !== auth()->id()) {
            return back()->with('error', 'Você não pode finalizar um ticket que não está atendendo.');
        }

        $ticket->update([
            'status' => 'finalizado',
            'finished_at' => now(),
        ]);

        event(new TicketUpdated($ticket));

        return back()->with('success', 'Ticket finalizado.');
    }

    public function cancel(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->attendant_id !== auth()->id()) {
            return back()->with('error', 'Você não pode cancelar um ticket que não está atendendo.');
        }

        $ticket->update([
            'status' => 'cancelado',
            'finished_at' => now(),
        ]);

        event(new TicketUpdated($ticket));

        return back()->with('success', 'Ticket cancelado.');
    }

    public function advance(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        // ✅ Verifica se o atendente é quem está atendendo
        if ($ticket->attendant_id !== auth()->id()) {
            return back()->with('error', 'Você não pode avançar um ticket que não está atendendo.');
        }

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

            return back()->with('success', 'Ticket avançado para atendimento.');
        }

        return back()->with('error', 'Este ticket não está na triagem.');
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
