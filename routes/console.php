<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Vence cupones y cierra reservas de fechas pasadas (ver ExpireStaleRecords)
Schedule::command('nanpuno:expire')->dailyAt('03:00')->withoutOverlapping();

// Recordatorio de las salidas confirmadas de mañana (a las 17:00 en Puno, ver SendBookingReminders)
Schedule::command('bookings:remind')->dailyAt('17:00')->timezone('America/Lima')->withoutOverlapping();
