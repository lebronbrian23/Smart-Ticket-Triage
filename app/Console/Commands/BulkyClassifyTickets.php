<?php

namespace App\Console\Commands;

use App\Jobs\ClassifyTicket;
use App\Models\Ticket;
use Illuminate\Console\Command;

class BulkyClassifyTickets extends Command
{
    /*
     * Copyright (c) 2025 Brian Ssekalegga
    * All rights reserved.
    * This code is submitted as part of a take-home assignment.
    * Redistribution or modification without author's permission is prohibited.
    */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tickets:bulky-classify {--missing-only=true : Only classify tickets without explanations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatching queued classification jobs for many tickets';
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $missing_only = filter_var($this->option('missing-only'), FILTER_VALIDATE_BOOLEAN);
        $query = Ticket::query();
        if ($missing_only) {
            $query->whereNull('explanation');
        }
        $count = 0;
        $query->chunkById(100, function ($tickets) use (&$count) {
            foreach( $tickets as $ticket ) {
                ClassifyTicket::dispatch($ticket->id);
                $count++;
            }
        });
        $this->info("Dispatched {$count} jobs.");
        return self::SUCCESS;
    }
}
