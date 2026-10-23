<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class AlmanacEntry extends Model
{
    public const KINDS = ['fiesta', 'ritual', 'agricola', 'civica'];

    protected $fillable = [
        'title',
        'description',
        'kind',
        'place_name',
        'province',
        'latitude',
        'longitude',
        'month',
        'day',
        'duration_days',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'month' => 'integer',
        'day' => 'integer',
        'duration_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Fechas en que ocurre dentro de un rango (puede caer en dos años si el rango cruza enero)
     *
     * @return array<int, array{starts: Carbon, ends: Carbon}>
     */
    public function occurrencesBetween(Carbon $from, Carbon $to): array
    {
        $out = [];
        foreach (range($from->year - 1, $to->year) as $year) {
            // 29 de febrero en años no bisiestos: se celebra el 28
            $day = min($this->day, Carbon::create($year, $this->month, 1)->daysInMonth);
            $starts = Carbon::create($year, $this->month, $day)->startOfDay();
            $ends = $starts->copy()->addDays(max(1, $this->duration_days) - 1)->endOfDay();

            if ($ends->gte($from) && $starts->lte($to)) {
                $out[] = ['starts' => $starts, 'ends' => $ends];
            }
        }

        return $out;
    }
}
