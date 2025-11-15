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
        Schema::create('detalles_carritos', function (Blueprint $table) {
            $table->id();
            $table->integer('cantidad');
            $table->decimal('subtotal', 8, 2);
            $table->date('fecha_adicion');

            // Relaciones correctas
            $table->foreignId('prenda_id')
                ->constrained('prendas')
                ->onDelete('cascade');

            $table->foreignId('carrito_id')
                ->constrained('carritos')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_carritos');
    }
};
