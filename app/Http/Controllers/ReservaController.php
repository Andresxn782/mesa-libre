<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservaRequest;
use App\Models\Mesa;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // GET /reservas?fecha=2026-10-12 → reservas de un día (hoy por defecto)
    public function index(Request $request)
    {
        $datos = $request->validate(['fecha' => ['nullable', 'date_format:Y-m-d']]);
        $fecha = $datos['fecha'] ?? today()->toDateString();

        // with('mesa') trae todas las mesas en UNA consulta extra.
        // Sin él, se haría una consulta por cada reserva al mostrar
        // su mesa (el problema "N+1").
        $reservas = Reserva::with('mesa')
            ->where('fecha', $fecha)
            ->orderBy('hora')
            ->get();

        return view('reservas.index', compact('reservas', 'fecha'));
    }

    // GET /reservas/create → formulario vacío
    public function create()
    {
        $mesas = Mesa::orderBy('numero')->get();

        return view('reservas.create', compact('mesas'));
    }

    // POST /reservas → guarda la reserva nueva
    public function store(ReservaRequest $request)
    {
        // Si llegamos aquí, los datos ya están validados
        $reserva = Reserva::create($request->validated());

        return redirect()
            ->route('reservas.index', ['fecha' => $reserva->fecha->toDateString()])
            ->with('success', 'Reserva creada correctamente.');
    }

    // GET /reservas/{reserva} → detalle de una reserva
    public function show(Reserva $reserva)
    {
        $reserva->load('mesa');

        return view('reservas.show', compact('reserva'));
    }

    // GET /reservas/{reserva}/edit → formulario con los datos actuales
    public function edit(Reserva $reserva)
    {
        $mesas = Mesa::orderBy('numero')->get();

        return view('reservas.edit', compact('reserva', 'mesas'));
    }

    // PUT /reservas/{reserva} → guarda los cambios
    public function update(ReservaRequest $request, Reserva $reserva)
    {
        $reserva->update($request->validated());

        return redirect()
            ->route('reservas.index', ['fecha' => $reserva->fecha->toDateString()])
            ->with('success', 'Reserva actualizada correctamente.');
    }

    // PATCH /reservas/{reserva}/cancelar → cambia el estado, no borra
    public function cancelar(Reserva $reserva)
    {
        $reserva->update(['estado' => 'cancelada']);

        return back()->with('success', 'Reserva cancelada.');
    }
}
