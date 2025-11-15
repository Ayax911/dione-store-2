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
        Schema::create('huellas_carbonos', function (Blueprint $table) {
            $table->id();
            $table->decimal('huella_nueva', 10, 4);
            $table->decimal('huella_reusada', 10, 4);

            
            $table->foreignId('prenda_id')->references('id')->on('prendas')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('huella_carbonos');
    }
};
