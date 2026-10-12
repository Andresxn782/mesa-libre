<?php

namespace App\Http\Controllers;

use App\Models\Mesa;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PlanoController extends Controller
{
    // GET /plano?fecha=2026-10-12&hora=21:00 → estado de cada mesa en ese momento
    public function __invoke(Request $request)
    {
        $datos = $request->validate([
            'fecha' => ['nullable', 'date_format:Y-m-d'],
            'hora'  => ['nullable', 'date_format:H:i'],
        ]);

        // Por defecto: ahora mismo
        $fecha = $datos['fecha'] ?? today()->toDateString();
        $hora  = $datos['hora'] ?? now()->format('H:i');
        $momento = Carbon::parse("{$fecha} {$hora}");

        // Todas las mesas con sus reservas activas de ese día, en solo 2 consultas
        $mesas = Mesa::with(['reservas' => fn ($consulta) => $consulta
                ->where('fecha', $fecha)
                ->where('estado', '!=', 'cancelada')
                ->orderBy('hora')])
            ->orderBy('numero')
            ->get();

        // Para cada mesa calculamos su estado; el resultado es un array por mesa
        $plano = $mesas->map(fn (Mesa $mesa) => $this->estadoDeMesa($mesa, $fecha, $momento));

        return view('plano', compact('plano', 'fecha', 'hora'));
    }

    // Decide si una mesa está ocupada, reservada o libre en un momento dado
    private function estadoDeMesa(Mesa $mesa, string $fecha, Carbon $momento): array
    {
        $proxima = null;

        // Las reservas vienen ordenadas por hora
        foreach ($mesa->reservas as $reserva) {
            $inicio = Carbon::parse("{$fecha} {$reserva->hora}");
            $fin = $inicio->copy()->addMinutes(Reserva::DURACION_MINUTOS);

            // Ocupada: el momento elegido cae dentro de la franja de una reserva
            if ($momento->gte($inicio) && $momento->lt($fin)) {
                return ['mesa' => $mesa, 'estado' => 'ocupada', 'reserva' => $reserva, 'fin' => $fin];
            }

            // Guardamos la primera reserva que empieza después del momento elegido
            if ($proxima === null && $inicio->gt($momento)) {
                $proxima = $reserva;
            }
        }

        if ($proxima) {
            return ['mesa' => $mesa, 'estado' => 'reservada', 'reserva' => $proxima, 'fin' => null];
        }

        return ['mesa' => $mesa, 'estado' => 'libre', 'reserva' => null, 'fin' => null];
    }
}
