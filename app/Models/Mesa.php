<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mesa extends Model
{
    // Campos que se pueden rellenar de golpe con create() o update().
    // Protege contra que alguien cuele campos extra desde un formulario.
    protected $fillable = ['numero', 'capacidad'];

    // Una mesa tiene muchas reservas
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }
}
