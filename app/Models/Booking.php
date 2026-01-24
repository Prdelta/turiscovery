<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'experiencia_id',
        'booking_date',
        'participants',
        'total_price',
        'status',
        'notes',
        'contact_phone',
        'contact_email',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'total_price' => 'decimal:2',
    ];

    /**
     * Relación con el usuario que hizo la reserva
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con la experiencia reservada
     */
    public function experiencia()
    {
        return $this->belongsTo(Experiencia::class);
    }

    /**
     * Scope para obtener solo reservas pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope para obtener solo reservas confirmadas
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope para obtener reservas activas (pending o confirmed)
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'confirmed']);
    }

    /**
     * Verificar si la reserva puede ser cancelada
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed'])
            && $this->booking_date->isFuture();
    }

    /**
     * Cancelar la reserva
     */
    public function cancel()
    {
        if ($this->canBeCancelled()) {
            $this->status = 'cancelled';
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Confirmar la reserva
     */
    public function confirm()
    {
        if ($this->status === 'pending') {
            $this->status = 'confirmed';
            $this->save();
            return true;
        }
        return false;
    }
}
