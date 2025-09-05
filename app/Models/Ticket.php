<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    /*
     * Copyright (c) 2025 Brian Ssekalegga
    * All rights reserved.
    * This code is submitted as part of a take-home assignment.
    * Redistribution or modification without author's permission is prohibited.
    */

    use HasFactory, HasUlids;

    protected $table = 'tickets';
    protected $fillable = ['subject','status','body', 'category', 'category_is_manual', 'explanation','confidence', 'note'];

    protected $casts = [
        'confidence' => 'float',
        'category_is_manual' => 'boolean',
    ];
}
