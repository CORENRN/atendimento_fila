<?php

namespace App\Events;

use App\Models\Ticket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class TicketUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('queue');
    }

    public function broadcastAs(): string
    {
        return 'ticket.updated';
    }
}
