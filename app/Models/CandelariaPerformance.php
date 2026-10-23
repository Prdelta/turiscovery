<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandelariaPerformance extends Model
{
    public const STATUSES = ['pending', 'performing', 'done', 'skipped'];

    protected $fillable = [
        'program_item_id',
        'candelaria_danza_id',
        'group_name',
        'order_number',
        'status',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'order_number' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function danza(): BelongsTo
    {
        return $this->belongsTo(CandelariaDanza::class, 'candelaria_danza_id');
    }
}
