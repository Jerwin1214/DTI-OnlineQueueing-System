<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    // Table name (optional if Laravel follows naming conventions)
    protected $table = 'queues';

    // Mass assignable fields
    protected $fillable = [
        'ticket_number',
        'status',      // e.g., 'waiting', 'serving', 'done'
        'counter_id',  // integer 1-5 or null
    ];

    // Optional: cast counter_id to integer
    protected $casts = [
        'counter_id' => 'integer',
    ];

    /**
     * Scope for currently serving tickets
     */
    public function scopeServing($query)
    {
        return $query->where('status', 'serving');
    }

    /**
     * Scope for waiting tickets
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }
}
