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
        Schema::create('prendas', function (Blueprint $table) {
            $table->id();
            
            
            $table->text('descripcion');
            
            $table->string('talla', 10);
            
            
            $table->decimal('precio', 10, 2);
            
            $table->string('material', 50);
            $table->string('titulo', 50);

            // Llaves foráneas
            $table->foreignId('categoria_id')
                  ->constrained('categorias')
                  ->onDelete('cascade');
                  
            $table->foreignId('usuario_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prendas');
    }
};