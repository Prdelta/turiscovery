<?php

namespace Database\Seeders;

use App\Models\AlmanacEntry;
use Illuminate\Database\Seeder;

/**
 * Fechas de partida del almanaque. Son celebraciones conocidas del departamento de Puno;
 * el equipo debe revisar cada año las fechas exactas y agregar las fiestas patronales
 * de cada provincia desde el panel de administración.
 */
class AlmanacSeeder extends Seeder
{
    public function run(): void
    {
        $entries = [
            [
                'title' => 'Fiesta de la Virgen de la Candelaria: día central',
                'description' => 'Misa y procesión de la Virgen. Las danzas continúan durante la primera mitad de febrero.',
                'kind' => 'fiesta', 'place_name' => 'Santuario de la Virgen de la Candelaria', 'province' => 'Puno',
                'latitude' => -15.8402, 'longitude' => -70.0219, 'month' => 2, 'day' => 2, 'duration_days' => 1,
            ],
            [
                'title' => 'Fiesta de las Cruces y feria de Alasitas',
                'description' => 'Se bendicen cruces y se compran miniaturas de los deseos para el año.',
                'kind' => 'fiesta', 'place_name' => 'Puno', 'province' => 'Puno',
                'latitude' => -15.8402, 'longitude' => -70.0219, 'month' => 5, 'day' => 3, 'duration_days' => 1,
            ],
            [
                'title' => 'Año Nuevo Andino',
                'description' => 'Solsticio de invierno: se recibe el primer sol del año andino al amanecer.',
                'kind' => 'ritual', 'place_name' => 'Altiplano', 'province' => null,
                'latitude' => null, 'longitude' => null, 'month' => 6, 'day' => 21, 'duration_days' => 1,
            ],
            [
                'title' => 'Fiesta de Santiago en Taquile',
                'description' => 'La fiesta principal de la isla, con danzas y los tejidos de gala de sus comunidades.',
                'kind' => 'fiesta', 'place_name' => 'Isla Taquile', 'province' => 'Puno',
                'latitude' => -15.77, 'longitude' => -69.685, 'month' => 7, 'day' => 25, 'duration_days' => 1,
            ],
            [
                'title' => 'Mes de la Pachamama',
                'description' => 'Durante agosto las familias hacen ofrendas a la tierra antes de la siembra.',
                'kind' => 'ritual', 'place_name' => 'Altiplano', 'province' => null,
                'latitude' => null, 'longitude' => null, 'month' => 8, 'day' => 1, 'duration_days' => 31,
            ],
            [
                'title' => 'Semana Jubilar de Puno',
                'description' => 'Aniversario de la fundación de Puno, con la escenificación de la salida de Manco Cápac y Mama Ocllo del lago.',
                'kind' => 'civica', 'place_name' => 'Puno', 'province' => 'Puno',
                'latitude' => -15.8402, 'longitude' => -70.0219, 'month' => 11, 'day' => 4, 'duration_days' => 2,
            ],
        ];

        foreach ($entries as $entry) {
            AlmanacEntry::firstOrCreate(
                ['title' => $entry['title']],
                $entry + ['is_active' => true],
            );
        }
    }
}
