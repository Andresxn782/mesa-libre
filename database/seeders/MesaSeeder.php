<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
  {
    // [número de mesa => capacidad]
    $mesas = [1 => 2, 2 => 2, 3 => 4, 4 => 4, 5 => 4, 6 => 6, 7 => 6, 8 => 8];

    foreach ($mesas as $numero => $capacidad) {
        \App\Models\Mesa::create(['numero' => $numero, 'capacidad' => $capacidad]);
    }
  }
}
