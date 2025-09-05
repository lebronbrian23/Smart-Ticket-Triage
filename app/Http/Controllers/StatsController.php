<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StatsController extends Controller
{
    /*
     * Copyright (c) 2025 Brian Ssekalegga
    * All rights reserved.
    * This code is submitted as part of a take-home assignment.
    * Redistribution or modification without author's permission is prohibited.
    */
    /**
     * Return aggregated ticket statistics for the dashboard.
     *
     * @return array<string,mixed>
     */
    public function index()
    {
        // Count tickets by status
        $status = Ticket::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Count tickets by category (null => "uncategorized")
        $category = Ticket::select(DB::raw("coalesce(category, 'uncategorized') as category"), DB::raw('count(*) as count'))
            ->groupBy('category')
            ->pluck('count', 'category');

        return [
            'status'   => $status,
            'category' => $category,
            'total'    => Ticket::count(),
        ];
    }
}
