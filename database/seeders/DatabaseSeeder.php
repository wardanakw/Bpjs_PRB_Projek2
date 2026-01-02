<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil seeder user yang sudah kamu buat
        $this->call(UserSeeder::class);
         $this->call(RelasiFktpApotekSeeder::class);
    }
}
