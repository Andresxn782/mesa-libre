<?php

namespace App\Http\Requests;

use App\Models\Mesa;
use App\Models\Reserva;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ReservaRequest extends FormRequest
{
    // Quién puede enviar este formulario. Devolvemos true porque
    // las rutas ya exigen haber iniciado sesión (middleware auth).
    public function authorize(): bool
    {
        return true;
    }

    // Reglas básicas de cada campo
    public function rules(): array
    {
        // POST = crear; PUT/PATCH = editar
        $esNueva = $this->isMethod('post');

        $reglasFecha = ['required', 'date_format:Y-m-d'];
        if ($esNueva) {
            // Al crear no se permiten fechas pasadas; al editar sí,
            // por si hay que corregir una reserva antigua
            $reglasFecha[] = 'after_or_equal:today';
        }

        return [
            'mesa_id'        => ['required', 'integer', 'exists:mesas,id'],
            'nombre_cliente' => ['required', 'string', 'max:100'],
            'telefono'       => ['required', 'regex:/^\+?[0-9 ]{9,20}$/'],
            'personas'       => ['required', 'integer', 'min:1', 'max:20'],
            'fecha'          => $reglasFecha,
            'hora'           => ['required', 'date_format:H:i'],
            // "sometimes": solo se valida si viene en el formulario (en el de editar)
            'estado'         => ['sometimes', Rule::in(Reserva::ESTADOS)],
        ];
    }

    // Reglas de negocio: se ejecutan después de las básicas
    public function after(): array
    {
        return [
            function (Validator $validator) {
                // Si ya falla algo básico (mesa inexistente, hora mal escrita...),
                // no tiene sentido seguir comprobando
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $mesa = Mesa::find($this->input('mesa_id'));

                // Regla 1: no más personas que la capacidad de la mesa
                if ((int) $this->input('personas') > $mesa->capacidad) {
                    $validator->errors()->add(
                        'personas',
                        "La mesa {$mesa->numero} admite como máximo {$mesa->capacidad} personas."
                    );
                }

                // Una reserva cancelada no ocupa la mesa: no hace falta comprobar solapes
                if ($this->input('estado') === 'cancelada') {
                    return;
                }

                // Regla 2: la mesa no puede tener otra reserva activa que se solape
                if ($this->mesaOcupada()) {
                    $horas = Reserva::DURACION_MINUTOS / 60;
                    $validator->errors()->add(
                        'hora',
                        "La mesa {$mesa->numero} ya está reservada en esa franja (cada reserva dura {$horas} h)."
                    );
                }
            },
        ];
    }

    // Comprueba si hay otra reserva activa en la misma mesa y día
    // que empiece a menos de DURACION_MINUTOS de esta
    private function mesaOcupada(): bool
    {
        $hora = Carbon::createFromFormat('H:i', $this->input('hora'));

        // Franja de conflicto: desde 2 h antes hasta 2 h después.
        // max()/min() la recortan a medianoche para no saltar al día siguiente.
        $desde = $hora->copy()->subMinutes(Reserva::DURACION_MINUTOS)
            ->max($hora->copy()->startOfDay())->format('H:i:s');
        $hasta = $hora->copy()->addMinutes(Reserva::DURACION_MINUTOS)
            ->min($hora->copy()->endOfDay())->format('H:i:s');

        return Reserva::where('mesa_id', $this->input('mesa_id'))
            ->where('fecha', $this->input('fecha'))
            ->where('estado', '!=', 'cancelada')
            ->where('hora', '>', $desde)
            ->where('hora', '<', $hasta)
            // Al editar, la propia reserva no cuenta como conflicto consigo misma
            ->when($this->route('reserva'), fn ($consulta, $reserva) => $consulta->whereKeyNot($reserva->id))
            ->exists();
    }

    // Mensajes de error en español
    public function messages(): array
    {
        return [
            'required'             => 'El campo :attribute es obligatorio.',
            'integer'              => 'El campo :attribute debe ser un número entero.',
            'exists'               => 'La :attribute seleccionada no existe.',
            'date_format'          => 'El formato de :attribute no es válido.',
            'in'                   => 'El :attribute no es válido.',
            'nombre_cliente.max'   => 'El nombre no puede superar los 100 caracteres.',
            'telefono.regex'       => 'Escribe un teléfono válido (de 9 a 20 dígitos, puede empezar por +).',
            'personas.min'         => 'Debe haber al menos 1 persona.',
            'personas.max'         => 'Como máximo 20 personas por reserva.',
            'fecha.after_or_equal' => 'No se puede reservar en una fecha pasada.',
        ];
    }

    // Nombres legibles de los campos para los mensajes
    public function attributes(): array
    {
        return [
            'mesa_id'        => 'mesa',
            'nombre_cliente' => 'nombre del cliente',
            'telefono'       => 'teléfono',
            'personas'       => 'número de personas',
        ];
    }
}
