<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'Admin Turiscovery',
            'email' => 'admin@turiscovery.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Socio users
        User::create([
            'name' => 'Carlos Mendoza',
            'email' => 'carlos@turiscovery.com',
            'password' => Hash::make('password'),
            'role' => 'socio',
            'phone' => '+51 987 654 321',
            'bio' => 'Propietario de varios locales en el centro de Puno',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'María López',
            'email' => 'maria@turiscovery.com',
            'password' => Hash::make('password'),
            'role' => 'socio',
            'phone' => '+51 912 345 678',
            'bio' => 'Organizadora de eventos culturales en Puno',
            'email_verified_at' => now(),
        ]);

        // Tourist users
        User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => Hash::make('password'),
            'role' => 'tourist',
            'phone' => '+51 999 888 777',
            'bio' => 'Amante de la cultura andina y la gastronomía peruana',
            'preferences' => ['gastronomy', 'culture', 'nature'],
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Ana García',
            'email' => 'ana@example.com',
            'password' => Hash::make('password'),
            'role' => 'tourist',
            'phone' => '+51 988 777 666',
            'bio' => 'Viajera frecuente interesada en experiencias auténticas',
            'preferences' => ['adventure', 'photography', 'local_food'],
            'email_verified_at' => now(),
        ]);

        // Generate additional random tourists using factory
        User::factory()->count(10)->create([
            'role' => 'tourist',
        ]);

        // Generate additional random socios using factory
        User::factory()->count(3)->create([
            'role' => 'socio',
        ]);
    }
}
