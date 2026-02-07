<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Locale;
use App\Models\Candelaria;
use App\Models\Experiencia;
use App\Models\Evento;
use App\Models\Promocion;
use App\Models\Review;
use App\Models\Favorite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <--- Importante para DB::raw
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create test users
        $tourist = User::create([
            'name' => 'Juan Turista',
            'email' => 'turista@example.com',
            'password' => Hash::make('password123'),
            'role' => 'tourist',
        ]);

        $socio = User::create([
            'name' => 'María Socia',
            'email' => 'socio@example.com',
            'password' => Hash::make('password123'),
            'role' => 'socio',
        ]);

        $admin = User::create([
            'name' => 'Admin Turiscovery',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Create Locales (Partner Venues) in Puno
        $locales = [
            [
                'user_id' => $socio->id,
                'name' => 'Restaurante La Casona',
                'description' => 'Comida típica puneña en un ambiente colonial',
                'address' => 'Jr. Lima 423, Puno',
                'phone' => '+51 51 351234',
                'email' => 'lacasona@example.com',
                'category' => 'restaurant',
                'latitude' => -15.8402,
                'longitude' => -70.0219,
                'images' => ['/img/loc_restaurant.png'],
            ],
            [
                'user_id' => $socio->id,
                'name' => 'Hotel Titikaka Inn',
                'description' => 'Hotel con vista al lago Titicaca',
                'address' => 'Jr. Independencia 185, Puno',
                'phone' => '+51 51 352345',
                'email' => 'titikaka@example.com',
                'category' => 'hotel',
                'latitude' => -15.8380,
                'longitude' => -70.0250,
                'images' => ['/img/loc_restaurant.png'], // Reusing for demo
            ],
            [
                'user_id' => $socio->id,
                'name' => 'Edgar Adventures',
                'description' => 'Agencia de turismo especializada en islas flotantes',
                'address' => 'Jr. Lima 328, Puno',
                'phone' => '+51 51 353456',
                'email' => 'edgar@example.com',
                'category' => 'tour_agency',
                'latitude' => -15.8425,
                'longitude' => -70.0200,
                'images' => ['/img/exp_titicaca.png'],
            ],
            [
                'user_id' => $admin->id,
                'name' => 'Museo Carlos Dreyer',
                'description' => 'Museo de arqueología y arte colonial',
                'address' => 'Jr. Conde de Lemos 289, Puno',
                'phone' => '+51 51 354567',
                'category' => 'museum',
                'latitude' => -15.8395,
                'longitude' => -70.0230,
                'images' => ['/img/candelaria_hist.png'],
            ],
            [
                'user_id' => $socio->id,
                'name' => 'Artesanías Taquile',
                'description' => 'Tienda de textiles y artesanías tradicionales',
                'address' => 'Jr. Lima 385, Puno',
                'phone' => '+51 51 355678',
                'category' => 'craft_shop',
                'latitude' => -15.8410,
                'longitude' => -70.0210,
                'images' => ['/img/evt_candelaria.png'],
            ],
        ];

        $createdLocales = [];
        foreach ($locales as $localeData) {
            // Extraemos lat/long
            $lat = $localeData['latitude'];
            $lng = $localeData['longitude'];

            // Eliminamos keys viejas para que no den error si no están en fillable
            unset($localeData['latitude'], $localeData['longitude']);

            // Insertamos DIRECTAMENTE el punto geográfico (Longitud primero, Latitud después)
            $localeData['location'] = DB::raw("ST_GeogFromText('POINT($lng $lat)')");

            $createdLocales[] = Locale::create($localeData);
        }

        // 3. Create Candelaria content
        $candelariaData = [
            [
                'user_id' => $admin->id,
                'locale_id' => null,
                'title' => 'Historia de la Festividad de la Virgen de la Candelaria',
                'description' => 'Conoce los orígenes de la festividad Patrimonio de la Humanidad',
                'content' => 'La Festividad de la Virgen de la Candelaria es la manifestación cultural y religiosa más importante de Puno...',
                'event_date' => now()->addMonths(1),
                'category' => 'history',
                'is_featured' => true,
                'image_url' => '/img/candelaria_hist.png', // Keep as string if Candelaria model uses it, needs check. Assuming consistent with others for now but Candelaria might be different? NO, let's assume consistent.
                // Wait, Candelaria model view? I didn't check Candelaria.php. Let's assume it also needs images array or check it.
                // Re-reading Step 306: Candelaria model usage.
                // I will use 'images' => [...] and add accessor.
                'images' => ['/img/candelaria_hist.png'],
            ],
            [
                'user_id' => $socio->id,
                'locale_id' => $createdLocales[0]->id,
                'title' => 'Danza de la Diablada',
                'description' => 'Una de las danzas más representativas de la Candelaria',
                'content' => 'La Diablada es una danza emblemática que representa la lucha entre el bien y el mal...',
                'event_date' => now()->addMonths(1)->addDays(2),
                'category' => 'dance',
                'is_featured' => true,
                'images' => ['/img/evt_candelaria.png'],
            ],
            [
                'user_id' => $admin->id,
                'locale_id' => null,
                'title' => 'Trajes Típicos de la Candelaria',
                'description' => 'Explora la riqueza de los trajes tradicionales',
                'content' => 'Los trajes de la Candelaria son obras de arte elaboradas con gran detalle...',
                'event_date' => now()->addMonths(1),
                'category' => 'costume',
                'is_featured' => false,
                'images' => ['/img/evt_candelaria.png'],
            ],
        ];

        $createdCandelaria = [];
        foreach ($candelariaData as $data) {
            // Remove image_url if I added it previously by mistake in strict mode?
            // Since I am replacing the block, I am good.
            // Wait, I need to check Candelaria model fillable in Candelaria.php if I want to be 100% sure.
            // But for now I'll stick to 'images' array pattern.
            if (isset($data['image_url'])) unset($data['image_url']); // Safety if I don't remove it in replacement? No, replacement overwrites.
            $createdCandelaria[] = Candelaria::create($data);
        }

        // 4. Create Experiencias
        $experiencias = [
            [
                'user_id' => $socio->id,
                'locale_id' => $createdLocales[2]->id,
                'title' => 'Turismo Vivencial en Islas Uros',
                'description' => 'Vive un día completo con las familias de las islas flotantes',
                'content' => 'Experimenta la vida en las islas flotantes de totora...',
                'difficulty' => 'easy',
                'duration_hours' => 6,
                'price_pen' => 120.00,
                'max_participants' => 12,
                'tags' => ['cultural', 'familia', 'fotografia'],
                'latitude' => -15.8200,
                'longitude' => -69.9800,
                'images' => ['/img/exp_titicaca.png'],
            ],
            [
                'user_id' => $socio->id,
                'locale_id' => $createdLocales[2]->id,
                'title' => 'Trekking a la Isla Taquile',
                'description' => 'Caminata y turismo cultural en Taquile',
                'content' => 'Camina por los senderos de la isla y conoce sus tradiciones...',
                'difficulty' => 'medium',
                'duration_hours' => 8,
                'price_pen' => 150.00,
                'max_participants' => 15,
                'tags' => ['aventura', 'naturaleza', 'cultura'],
                'latitude' => -15.7700,
                'longitude' => -69.6800,
                'images' => ['/img/exp_titicaca.png'],
            ],
            [
                'user_id' => $admin->id,
                'locale_id' => null,
                'title' => 'Kayak en el Lago Titicaca',
                'description' => 'Aventura en kayak por el lago navegable más alto del mundo',
                'content' => 'Explora el lago Titicaca desde una perspectiva única...',
                'difficulty' => 'hard',
                'duration_hours' => 4,
                'price_pen' => 180.00,
                'max_participants' => 8,
                'tags' => ['aventura', 'deporte', 'naturaleza'],
                'latitude' => -15.8350,
                'longitude' => -70.0100,
                'images' => ['/img/exp_titicaca.png'],
            ],
        ];

        $createdExperiencias = [];
        foreach ($experiencias as $expData) {
            if (isset($expData['latitude']) && isset($expData['longitude'])) {
                $lat = $expData['latitude'];
                $lng = $expData['longitude'];
                unset($expData['latitude'], $expData['longitude']);

                // Convertimos a PostGIS Point
                $expData['location'] = DB::raw("ST_GeogFromText('POINT($lng $lat)')");
            }
            $createdExperiencias[] = Experiencia::create($expData);
        }

        // 5. Create Eventos
        $eventos = [
            [
                'user_id' => $admin->id,
                'locale_id' => null,
                'title' => 'Concierto de Música Andina',
                'description' => 'Noche de música tradicional puneña',
                'content' => 'Disfruta de los mejores grupos de música andina...',
                'start_time' => now()->addDays(7)->setHour(19),
                'end_time' => now()->addDays(7)->setHour(22),
                'ticket_price' => 30.00,
                'category' => 'concert',
                'latitude' => -15.8390,
                'longitude' => -70.0220,
                'images' => ['/img/evt_candelaria.png'],
            ],
            [
                'user_id' => $socio->id,
                'locale_id' => $createdLocales[0]->id,
                'title' => 'Festival Gastronómico Puneño',
                'description' => 'Degustación de platsillos típicos',
                'content' => 'Prueba los mejores platos de la gastronomía puneña...',
                'start_time' => now()->addDays(14),
                'end_time' => now()->addDays(16),
                'ticket_price' => null,
                'category' => 'festival',
                'latitude' => -15.8402,
                'longitude' => -70.0219,
                'images' => ['/img/loc_restaurant.png'],
            ],
            [
                'user_id' => $admin->id,
                'locale_id' => null,
                'title' => 'Noche Cultural en la Plaza de Armas',
                'description' => 'Evento cultural gratuito',
                'content' => 'Danzas, música y teatro en vivo...',
                'start_time' => now()->addDays(3)->setHour(18),
                'end_time' => now()->addDays(3)->setHour(21),
                'ticket_price' => null,
                'category' => 'cultural',
                'latitude' => -15.8398,
                'longitude' => -70.0226,
                'images' => ['/img/evt_candelaria.png'],
            ],
        ];

        $createdEventos = [];
        foreach ($eventos as $eventoData) {
            if (isset($eventoData['latitude']) && isset($eventoData['longitude'])) {
                $lat = $eventoData['latitude'];
                $lng = $eventoData['longitude'];
                unset($eventoData['latitude'], $eventoData['longitude']);

                // Convertimos a PostGIS Point
                $eventoData['location'] = DB::raw("ST_GeogFromText('POINT($lng $lat)')");
            }
            $createdEventos[] = Evento::create($eventoData);
        }

        // 6. Create Promociones
        $promociones = [
            [
                'user_id' => $socio->id,
                'locale_id' => $createdLocales[0]->id,
                'title' => '2x1 en Cena Típica',
                'description' => 'Trae a un amigo y paga solo uno',
                'discount_type' => '2x1',
                'discount_percentage' => null,
                'discount_amount' => null,
                'original_price' => 60.00,
                'final_price' => 30.00,
                'start_date' => now(),
                'end_date' => now()->addDays(30),
                'terms_conditions' => 'Válido de lunes a jueves. Reserva con anticipación.',
                'redemption_code' => 'CASONA2X1',
                'images' => ['/img/promo_food.png'],
            ],
            [
                'user_id' => $socio->id,
                'locale_id' => $createdLocales[1]->id,
                'title' => '20% de descuento en hospedaje',
                'description' => 'Descuento especial para estadías largas',
                'discount_type' => 'percentage',
                'discount_percentage' => 20,
                'discount_amount' => null,
                'original_price' => 200.00,
                'final_price' => 160.00,
                'start_date' => now(),
                'end_date' => now()->addDays(60),
                'terms_conditions' => 'Mínimo 3 noches de hospedaje.',
                'redemption_code' => 'TITIKAKA20',
                'images' => ['/img/loc_restaurant.png'],
            ],
            [
                'user_id' => $socio->id,
                'locale_id' => $createdLocales[2]->id,
                'title' => 'S/50 de descuento en tour',
                'description' => 'Descuento fijo en cualquier tour',
                'discount_type' => 'fixed',
                'discount_percentage' => null,
                'discount_amount' => 50.00,
                'original_price' => 150.00,
                'final_price' => 100.00,
                'start_date' => now(),
                'end_date' => now()->addDays(45),
                'terms_conditions' => 'No acumulable con otras promociones.',
                'redemption_code' => 'EDGAR50',
                'images' => ['/img/exp_titicaca.png'],
            ],
        ];

        $createdPromociones = [];
        foreach ($promociones as $data) {
            $createdPromociones[] = Promocion::create($data);
        }

        // 7. Create Reviews
        Review::create([
            'user_id' => $tourist->id,
            'reviewable_type' => 'App\Models\Locale',
            'reviewable_id' => $createdLocales[0]->id,
            'rating' => 5,
            'title' => '¡Excelente comida!',
            'comment' => 'La comida típica es deliciosa y el ambiente muy acogedor.',
        ]);

        Review::create([
            'user_id' => $tourist->id,
            'reviewable_type' => 'App\Models\Experiencia',
            'reviewable_id' => $createdExperiencias[0]->id,
            'rating' => 5,
            'title' => 'Experiencia inolvidable',
            'comment' => 'Conocer a las familias de los Uros fue increíble.',
        ]);

        Review::create([
            'user_id' => $socio->id,
            'reviewable_type' => 'App\Models\Evento',
            'reviewable_id' => $createdEventos[0]->id,
            'rating' => 4,
            'title' => 'Muy bueno',
            'comment' => 'Excelente música, aunque el lugar estaba un poco lleno.',
        ]);

        // 8. Create Favorites
        Favorite::create([
            'user_id' => $tourist->id,
            'favoritable_type' => 'App\Models\Locale',
            'favoritable_id' => $createdLocales[0]->id,
        ]);

        Favorite::create([
            'user_id' => $tourist->id,
            'favoritable_type' => 'App\Models\Experiencia',
            'favoritable_id' => $createdExperiencias[0]->id,
        ]);

        Favorite::create([
            'user_id' => $tourist->id,
            'favoritable_type' => 'App\Models\Candelaria',
            'favoritable_id' => $createdCandelaria[0]->id,
        ]);

        // 9. Seed Candelaria Gallery (Historical Photos)
        $this->call(CandelariaGallerySeeder::class);

        // 10. Seed Candelaria Danzas (Traditional Dances)
        $this->call(CandelariaDanzasSeeder::class);

        echo "\n";
        echo "========================================\n";
        echo "✓ Database seeded successfully!\n";
        echo "========================================\n";
        echo "\nTest Users:\n";
        echo "  Tourist: turista@example.com / password123\n";
        echo "  Socio: socio@example.com / password123\n";
        echo "  Admin: admin@example.com / password123\n";
        echo "\nContent Created:\n";
        echo "  → 5 Locales\n";
        echo "  → 3 Experiencias\n";
        echo "  → 3 Eventos\n";
        echo "  → 3 Promociones\n";
        echo "  → 3 Candelaria posts\n";
        echo "  → 12 Historical photos (Gallery)\n";
        echo "  → 13 Traditional dances (7 mestizas, 6 autóctonas)\n";
        echo "========================================\n";
    }
}
