<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['tipo_prenda' => 'Camisetas'],
            ['tipo_prenda' => 'Pantalones'],
            ['tipo_prenda' => 'Vestidos'],
            ['tipo_prenda' => 'Faldas'],
            ['tipo_prenda' => 'Chaquetas'],
            ['tipo_prenda' => 'Abrigos'],
            ['tipo_prenda' => 'Sudaderas'],
            ['tipo_prenda' => 'Shorts'],
            ['tipo_prenda' => 'Jeans'],
            ['tipo_prenda' => 'Blusas'],
            ['tipo_prenda' => 'Zapatos'],
            ['tipo_prenda' => 'Accesorios'],
            ['tipo_prenda' => 'Ropa Deportiva'],
            ['tipo_prenda' => 'Ropa Interior'],
            ['tipo_prenda' => 'Trajes'],
        ];

        DB::table('categorias')->insert($categorias);
    }
}