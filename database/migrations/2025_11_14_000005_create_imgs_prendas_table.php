<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('imgs_prendas', function (Blueprint $table) {
            $table->id();
            $table->string('direccion_imagen', 255);
            
            $table->foreignId('prenda_id')
                  ->constrained('prendas')
                  ->onDelete('cascade');
            
            // Opcional: Si quieres timestamps
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imgs_prendas');
    }
};