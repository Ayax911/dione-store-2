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

            $table->decimal('co2_fabricacion', 10, 4)->nullable();
            $table->decimal('co2_total_nueva', 10, 4)->nullable();
            $table->decimal('co2_segunda_mano', 10, 4)->nullable();
            $table->decimal('co2_ahorrado', 10, 4)->nullable();
            $table->decimal('porcentaje_ahorro', 10, 4)->nullable();

            $table->string('categoria_calculo', 100)->nullable();

            $table->foreignId('prenda_id')
                ->constrained('prendas')
                ->onDelete('cascade');

            $table->timestamps();
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
