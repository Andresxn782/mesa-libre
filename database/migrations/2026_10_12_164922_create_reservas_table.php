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
    Schema::create('reservas', function (Blueprint $table) {
        $table->id();
        // Clave foránea a mesas.id; impide borrar una mesa que tenga reservas
        $table->foreignId('mesa_id')->constrained()->restrictOnDelete();
        $table->string('nombre_cliente', 100);
        $table->string('telefono', 20);
        $table->unsignedTinyInteger('personas');
        $table->date('fecha');
        $table->time('hora');
        $table->enum('estado', ['pendiente', 'confirmada', 'cancelada'])->default('pendiente');
        $table->timestamps();

        // Índice porque siempre filtraremos por fecha y mesa
        $table->index(['fecha', 'mesa_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
