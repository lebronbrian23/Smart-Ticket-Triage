<?php

namespace App\Http\Controllers;

use App\Jobs\ClassifyTicket;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{

    /*
     * Copyright (c) 2025 Brian Ssekalegga
    * All rights reserved.
    * This code is submitted as part of a take-home assignment.
    * Redistribution or modification without author's permission is prohibited.
    */

    /**
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function index(Request $request) {

        $search_query = $request->string('search')->toString();
        $status = array_filter(explode(',', (string) $request->query('status')));
        $category = array_filter(explode(',', (string) $request->query('category')));
        $per_page = (int) $request->query('per_page', 12);

        $query = Ticket::query();
        if ($search_query !== '') {
            $query->where(function ($w) use ($search_query) {
                $w->where('subject', 'like', '%' . $search_query . '%')
                ->orWhere('body', 'like', '%' . $search_query . '%')
                ->orWhere('status', 'like', '%' . $search_query . '%')
                ->orWhere('category', 'like', '%' . $search_query . '%');
            });
        }
        if ($status) {
            $query->whereIn('status', $status);
        }
        if ($category) {
            $query->whereIn('category', $category);
        }
        $query->latest();
        return $query->paginate($per_page);
    }

    /**
    * Show a single ticket.
    */
    public function show(string $id)
    {
        return Ticket::findOrFail($id);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        $data = $request->validate([
            'subject' => ['required','string','min:3','max:253'],
            'body' => ['required','string','min:4'],
            'status'  => ['sometimes','in:open,in_progress,resolved,closed'],
        ]);

        // default status if not passed
        $data['status'] = $data['status'] ?? 'open';

        $ticket = Ticket::create($data);
        return response()->json($ticket, 200);
    }

    /**
     * Update an existing ticket (status, category, note).
     */
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);

        $data = $request->validate([
            'status'   => [Rule::in(['open','in_progress','resolved','closed'])],
            'category' => ['sometimes','nullable','string','max:50'],
            'note'     => ['sometimes','nullable','string','max:2000'],
        ]);

        // If category is updated manually, mark it as manual
        if (array_key_exists('category', $data)) {
            $ticket->category = $data['category'];
            $ticket->category_is_manual = true;
            unset($data['category']);
        }

        $ticket->fill($data)->save();

        return $ticket;
    }

    /**
     * Dispatch classification job for a ticket.
     */
    public function classify(string $id)
    {
        $ticket = Ticket::findOrFail($id);

        ClassifyTicket::dispatch($ticket->id);

        return response()->json(['queued' => true], 202);
    }
}
