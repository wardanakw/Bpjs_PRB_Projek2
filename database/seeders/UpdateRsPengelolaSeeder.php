<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateRsPengelolaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('patients')
            ->whereNull('rs_pengelola_prb')
            ->whereNotNull('rumah_sakit_id')
            ->update(['rs_pengelola_prb' => \DB::raw('rumah_sakit_id')]);

        $this->command->info('Updated rs_pengelola_prb for existing patients.');
    }
}
