<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Locale;
use App\Models\Experiencia;
use App\Models\Evento;
use App\Models\Promocion;
use App\Models\Candelaria;

class FixImagesSeeder extends Seeder
{
    public function run()
    {
        // 1. Locales
        $locales = Locale::all();
        foreach ($locales as $locale) {
            $image = '/img/loc_restaurant.png';
            if ($locale->category == 'tour_agency') {
                $image = '/img/exp_titicaca.png';
            } elseif ($locale->category == 'craft_shop') {
                $image = '/img/evt_candelaria.png';
            } elseif ($locale->category == 'museum') {
                $image = '/img/candelaria_hist.png';
            }

            $locale->images = [$image];
            $locale->save();
        }
        $this->command->info('Locales updated.');

        // 2. Experiencias
        $exps = Experiencia::all();
        foreach ($exps as $exp) {
            $exp->images = ['/img/exp_titicaca.png'];
            $exp->save();
        }
        $this->command->info('Experiencias updated.');

        // 3. Eventos
        $evts = Evento::all();
        foreach ($evts as $evt) {
            $image = '/img/evt_candelaria.png';
            if ($evt->category == 'festival' || $evt->category == 'gastronomy') {
                $image = '/img/loc_restaurant.png';
            }
            $evt->images = [$image];
            $evt->save();
        }
        $this->command->info('Eventos updated.');

        // 4. Promociones
        $promos = Promocion::all();
        foreach ($promos as $promo) {
            $image = '/img/promo_food.png';
            // Simple logic based on desc or just default
            if (stripos($promo->title, 'hospedaje') !== false) {
                $image = '/img/loc_restaurant.png';
            } elseif (stripos($promo->title, 'tour') !== false) {
                $image = '/img/exp_titicaca.png';
            }

            $promo->images = [$image];
            $promo->save();
        }
        $this->command->info('Promociones updated.');

        // 5. Candelaria
        $cande = Candelaria::all();
        foreach ($cande as $c) {
            $c->images = ['/img/evt_candelaria.png'];
            if ($c->category == 'history') {
                $c->images = ['/img/candelaria_hist.png'];
            }
            $c->save();
        }
        $this->command->info('Candelaria updated.');
    }
}
