<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminBendaharaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat akun admin
        User::factory()->admin()->create([
            'password' => bcrypt('admin123'), // bisa diganti passwordnya
        ]);

        // Buat akun bendahara
        User::factory()->bendahara()->create([
            'password' => bcrypt('bendahara123'), // bisa diganti passwordnya
        ]);
    }
}
