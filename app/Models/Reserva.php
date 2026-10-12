<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    // Duración fija de una reserva, en minutos. La usaremos para
    // saber si una mesa está ocupada a una hora concreta.
    public const DURACION_MINUTOS = 120;

    public const ESTADOS = ['pendiente', 'confirmada', 'cancelada'];

    protected $fillable = [
        'mesa_id', 'nombre_cliente', 'telefono',
        'personas', 'fecha', 'hora', 'estado',
    ];

    // Convierte la fecha en un objeto Carbon, para poder formatearla y compararla
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    // Cada reserva pertenece a una mesa
    public function mesa(): BelongsTo
    {
        return $this->belongsTo(Mesa::class);
    }
}
