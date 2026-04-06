<?php

namespace App\Http\Controllers;

use App\Events\TicketUpdated;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function queue($stage)
    {
        $statusValue = in_array($stage, ['triagem', 'atendimento', 'carteira']) ? $stage : 'triagem';
        $attendantId = auth()->id();

        $tickets = Ticket::where('stage', $stage)
            ->whereIn('status', ['aguardando', $statusValue])
            ->orderByRaw('called_at IS NULL, called_at ASC')
            ->orderBy('created_at')
            ->get();

        $services = [
            'inscricao' => 'Inscrição', 'renovacao' => 'Renovação', 'regularizacao' => 'Regularização',
            'transferencia' => 'Transferência', 'secundaria' => 'Secundária', 'especializacao' => 'Especialização',
            'cancelamento' => 'Cancelamento', 'remida' => 'Remida', 'reativacao' => 'Reativação',
            'certidao' => 'Certidão', 'financeiro' => 'Financeiro', 'documentacao' => 'Documentação',
            'informacao' => 'Informações', 'art' => 'ART', 'outros' => 'Outros',
        ];

        $calledTicket = Ticket::where('stage', $stage)
            ->where('attendant_id', $attendantId)
            ->where('status', $statusValue)
            ->whereNull('finished_at')
            ->first();

        $called_id = $calledTicket?->id;

        return view('queue', compact('tickets', 'stage', 'services', 'called_id', 'calledTicket'));
    }

    public function callNext($stage)
    {
        $attendantId = auth()->id();

        $existingCall = Ticket::where('attendant_id', $attendantId)
            ->whereNull('finished_at')
            ->first();

        if ($existingCall) {
            return back()->with('error', 'Você já está atendendo um ticket.');
        }
        
        $ticket = Ticket::where('stage', $stage)
            ->where('status', 'aguardando')
            ->orderBy('created_at')
            ->first();

        if ($ticket) {
            $data = [ 
                'attendant_id' => $attendantId,
                'last_called_at' => now(),
                'status' => $stage
            ];

            if ($stage === 'triagem') {
                if (!$ticket->called_tri_at) $data['called_tri_at'] = now();
                $data['triagem_id'] = $attendantId;
            } else {
                if (!$ticket->called_at) $data['called_at'] = now();
            }

            $ticket->update($data);
            event(new TicketUpdated($ticket));

            return back()->with('success', 'Ticket chamado.');
        }

        return back()->with('info', 'Fila vazia.');
    }

    public function callNextPriority($stage)
    {
        $attendantId = auth()->id();

        $existingCall = Ticket::where('attendant_id', $attendantId)
            ->whereNull('finished_at')
            ->first();

        if ($existingCall) {
            return back()->with('error', 'Você já está atendendo um ticket.');
        }

        $ticket = Ticket::where('stage', $stage)
            ->where('status', 'aguardando')
            ->where('type', 'preferencial')
            ->orderBy('created_at')
            ->first();

        if ($ticket) {
            $data = [
                'status' => $stage,
                'attendant_id' => $attendantId,
                'last_called_at' => now(),
            ];

            if ($stage === 'triagem') {
                if (!$ticket->called_tri_at) $data['called_tri_at'] = now();
                $data['triagem_id'] = $attendantId;
            } else {
                if (!$ticket->called_at) $data['called_at'] = now();
            }

            $ticket->update($data);
            event(new TicketUpdated($ticket));

            return back()->with('success', 'Prioritário chamado.');
        }

        return back()->with('info', 'Sem prioritários.');
    }

    public function finish(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->attendant_id !== auth()->id()) {
            return back()->with('error', 'Acesso negado.');
        }

        $ticket->update([
            'status' => 'finalizado',
            'finished_at' => now(),
        ]);

        event(new TicketUpdated($ticket));
        return back()->with('success', 'Finalizado.');
    }

    public function cancel(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->attendant_id !== auth()->id()) {
            return back()->with('error', 'Acesso negado.');
        }

        $ticket->update([
            'status' => 'cancelado',
            'finished_at' => now(),
        ]);

        event(new TicketUpdated($ticket));
        return back()->with('success', 'Cancelado.');
    }

    public function advance(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->attendant_id !== auth()->id()) {
            return back()->with('error', 'Acesso negado.');
        }

        $request->validate([
            'service' => 'required|in:inscricao,renovacao,regularizacao,transferencia,secundaria,especializacao,cancelamento,remida,reativacao,certidao,financeiro,documentacao,informacao,art,outros',
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
            return back()->with('success', 'Avançado.');
        }

        return back()->with('error', 'Ticket inválido.');
    }

    public function callMultiple(Request $request, $stage)
    {
        if ($stage !== 'carteira') {
            return back()->with('error', 'Operação inválida.');
        }

        $request->validate([
            'ticket_ids'   => 'required|array|min:1',
            'ticket_ids.*' => 'integer|exists:tickets,id',
        ]);

        $attendantId = auth()->id();

        $tickets = Ticket::whereIn('id', $request->ticket_ids)
            ->where('stage', 'carteira')
            ->where('status', 'aguardando')
            ->get();

        if ($tickets->isEmpty()) {
            return back()->with('info', 'Nenhum ticket válido selecionado.');
        }

        foreach ($tickets as $ticket) {
            $data = [
                'status'         => 'carteira',
                'attendant_id'   => $attendantId,
                'last_called_at' => now(),
            ];

            if (!$ticket->called_at) {
                $data['called_at'] = now();
            }

            $ticket->update($data);
            event(new TicketUpdated($ticket));
        }

        return back()->with('success', count($tickets) . ' ticket(s) chamado(s).');
    }

    public function recall($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update(['last_called_at' => now()]);
        event(new TicketUpdated($ticket->fresh()));
        return back()->with('success', 'Chamado novamente.');
    }

    public function takeTicket(Request $request)
    {
        $request->validate([
            'type' => 'required|in:regular,preferencial,carteira',
            'cpf'  => $request->type === 'carteira' ? 'required|min:14' : 'nullable'
        ]);

        $ticket = Ticket::create([
            'type' => $request->type,
            'stage' => $request->type === 'carteira' ? 'carteira' : 'triagem',
            'status' => 'aguardando',
            'cpf' => $request->cpf,
        ]);

        return redirect()->route('ticket.show', ['id' => $ticket->id]);
    }

    public function showTicket($id)
    {
        $ticket = Ticket::findOrFail($id);
        return view('ticket_show', compact('ticket'));
    }

    public function takeTicketView()
    {
        return view('ticket_take'); 
    }

    public function getTicketsJson($stage)
    {
        $statusValue = in_array($stage, ['triagem', 'atendimento', 'carteira']) ? $stage : 'triagem';
        $tickets = Ticket::where('stage', $stage)
            ->whereIn('status', ['aguardando', $statusValue])
            ->orderByRaw('called_at IS NULL, called_at ASC')
            ->orderBy('created_at')
            ->get();

        return response()->json(['tickets' => $tickets]);
    }
}