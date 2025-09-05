<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Services\TicketClassifier;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClassifyTicket implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $ticket_id)
    {
        //
    }

    public function backoff(): array {
        return [5, 15, 30, 60];
    }

    /**
     * Execute the job.
     */
    public function handle(TicketClassifier $classifier): void
    {
        $ticket = Ticket::findorFail($this->ticket_id);

        $result = $classifier->classifyTicket($ticket);

        //check if manual category is already set by user
        if ( !$ticket->category_is_manual ) {
            $ticket->category = $result['category'];
        }
        $ticket->explanation = $result['explanation'];
        $ticket->confidence = $result['confidence'];
        $ticket->save();
    }

}
