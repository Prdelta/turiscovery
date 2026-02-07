<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class CandelariaGallerySeeder extends Seeder
{
    /**
     * Seed the candelaria_gallery table with historical photos
     * Contenido agregado por el administrador
     */
    public function run(): void
    {
        // Obtener el admin que agregará el contenido
        $admin = User::where('role', 'admin')->first();

        if (!$admin) {
            $this->command->error('⚠️  No se encontró usuario admin. Ejecuta DatabaseSeeder primero.');
            return;
        }

        $galleries = [
            [
                'title' => 'Gran Corso de la Festividad 2024',
                'description' => 'Miles de danzarines recorrieron las calles de Puno en el desfile principal. La algarabía y colorido de más de 200 conjuntos folclóricos llenaron de alegría la ciudad.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/Virgen_de_la_Candelaria_Festival.jpg/1200px-Virgen_de_la_Candelaria_Festival.jpg',
                'year' => 2024,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Diablada Bellavista en la Plaza de Armas',
                'description' => 'El conjunto Diablada Bellavista ejecutando su tradicional danza en la Plaza de Armas de Puno. Sus trajes elaborados en oro y pedrería brillaban bajo el sol del altiplano.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Diablada_de_Puno.jpg/1200px-Diablada_de_Puno.jpg',
                'year' => 2024,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Morenada Central Puno 2023',
                'description' => 'La majestuosidad de la Morenada se hizo presente con sus característicos movimientos y trajes pesados que rinden homenaje a los trabajadores mineros de la época colonial.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f8/Morenada_Puno_Peru.jpg/1200px-Morenada_Puno_Peru.jpg',
                'year' => 2023,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Concurso de Trajes de Luces 2023',
                'description' => 'Competencia de trajes típicos donde los participantes muestran la creatividad y maestría en la confección de sus vestimentas tradicionales, heredadas de generación en generación.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1c/Trajes_tipicos_Candelaria_Puno.jpg/1200px-Trajes_tipicos_Candelaria_Puno.jpg',
                'year' => 2023,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => 'Festividad de la Virgen de la Candelaria 2022',
                'description' => 'Procesión religiosa de la Virgen de la Candelaria por las principales calles de Puno. Feligreses y danzarines acompañan a la patrona en un acto de fe y devoción.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Procesion_Virgen_Candelaria_Puno.jpg/1200px-Procesion_Virgen_Candelaria_Puno.jpg',
                'year' => 2022,
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Sikuris y Bandas en el Estadio 2022',
                'description' => 'Concurso de sikuris y bandas de músicos en el Estadio Enrique Torres Belón. La música autóctona del altiplano resonó con fuerza en este memorable encuentro.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/33/Sikuris_Puno_Festival.jpg/1200px-Sikuris_Puno_Festival.jpg',
                'year' => 2022,
                'order' => 6,
                'is_active' => true,
            ],
            [
                'title' => 'Waca Waca en las Calles de Puno 2021',
                'description' => 'El conjunto folklórico Waca Waca representando la danza que satiriza la corrida de toros española. Una muestra de resistencia cultural e identidad andina.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b2/Waca_Waca_Candelaria.jpg/1200px-Waca_Waca_Candelaria.jpg',
                'year' => 2021,
                'order' => 7,
                'is_active' => true,
            ],
            [
                'title' => 'Llamerada en el Gran Corso 2021',
                'description' => 'Danzarines de Llamerada rindiendo tributo a los criadores de llamas y alpacas del altiplano. Sus movimientos imitan el paso característico de estos camélidos andinos.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Llamerada_Puno.jpg/1200px-Llamerada_Puno.jpg',
                'year' => 2021,
                'order' => 8,
                'is_active' => true,
            ],
            [
                'title' => 'Festividad Patrimonio Cultural 2020',
                'description' => 'Celebración del reconocimiento de la Festividad de la Virgen de la Candelaria como Patrimonio Cultural Inmaterial de la Humanidad por la UNESCO en 2014.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/56/UNESCO_Candelaria_Puno.jpg/1200px-UNESCO_Candelaria_Puno.jpg',
                'year' => 2020,
                'order' => 9,
                'is_active' => true,
            ],
            [
                'title' => 'Danzas Autóctonas en Competencia 2020',
                'description' => 'Conjuntos de danzas autóctonas compitiendo en el atrio del templo de San Juan Bautista. La pureza de las danzas ancestrales cautivó al público presente.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Danzas_autoctonas_Puno.jpg/1200px-Danzas_autoctonas_Puno.jpg',
                'year' => 2020,
                'order' => 10,
                'is_active' => true,
            ],
            [
                'title' => 'Trajes Tradicionales en Exhibición 2019',
                'description' => 'Exposición de trajes típicos en el Museo Municipal. Cada pieza cuenta una historia de tradición, identidad y maestría artesanal transmitida por siglos.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Trajes_Candelaria_Museo.jpg/1200px-Trajes_Candelaria_Museo.jpg',
                'year' => 2019,
                'order' => 11,
                'is_active' => true,
            ],
            [
                'title' => 'Tinku en las Calles Puneñas 2019',
                'description' => 'La danza guerrera del Tinku representando el encuentro ritual de comunidades. Una expresión de fuerza, valentía y conexión con la Pachamama.',
                'image_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ab/Tinku_Puno_Candelaria.jpg/1200px-Tinku_Puno_Candelaria.jpg',
                'year' => 2019,
                'order' => 12,
                'is_active' => true,
            ],
        ];

        foreach ($galleries as $gallery) {
            DB::table('candelaria_gallery')->insert([
                'user_id' => $admin->id, // Agregado por el admin
                'title' => $gallery['title'],
                'description' => $gallery['description'],
                'image_url' => $gallery['image_url'],
                'year' => $gallery['year'],
                'order' => $gallery['order'],
                'is_active' => $gallery['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ Galería histórica creada: ' . count($galleries) . ' fotografías');
        $this->command->info('   → Agregadas por: ' . $admin->name . ' (Admin)');
    }
}
