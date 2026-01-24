<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventAttendee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'evento_id',
        'status',
        'guests',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evento()
    {
        return $this->belongsTo(Evento::class);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeMaybe($query)
    {
        return $query->where('status', 'maybe');
    }

    public function cancel()
    {
        $this->update(['status' => 'cancelled']);
    }

    public function confirm()
    {
        $this->update(['status' => 'confirmed']);
    }
}
