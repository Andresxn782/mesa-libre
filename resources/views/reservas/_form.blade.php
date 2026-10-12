{{-- Campos compartidos por crear y editar. $reserva solo existe al editar. --}}
@php($reserva = $reserva ?? null)

<div class="space-y-4">
    <div>
        <x-input-label for="nombre_cliente" value="Nombre del cliente" />
        {{-- old() recupera lo que el usuario escribió si la validación falla --}}
        <x-text-input id="nombre_cliente" name="nombre_cliente" type="text" class="mt-1 block w-full"
            :value="old('nombre_cliente', $reserva?->nombre_cliente)" required maxlength="100" />
        <x-input-error :messages="$errors->get('nombre_cliente')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="telefono" value="Teléfono" />
        <x-text-input id="telefono" name="telefono" type="tel" class="mt-1 block w-full"
            :value="old('telefono', $reserva?->telefono)" required />
        <x-input-error :messages="$errors->get('telefono')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <x-input-label for="fecha" value="Fecha" />
            {{-- Al crear desde el listado, propone la fecha que se estaba viendo --}}
            <x-text-input id="fecha" name="fecha" type="date" class="mt-1 block w-full"
                :value="old('fecha', $reserva?->fecha?->toDateString() ?? request('fecha', today()->toDateString()))" required />
            <x-input-error :messages="$errors->get('fecha')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="hora" value="Hora" />
            {{-- MySQL guarda "20:00:00"; el input necesita "20:00" --}}
            <x-text-input id="hora" name="hora" type="time" class="mt-1 block w-full"
                :value="old('hora', $reserva ? substr($reserva->hora, 0, 5) : '')" required />
            <x-input-error :messages="$errors->get('hora')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="personas" value="Personas" />
            <x-text-input id="personas" name="personas" type="number" min="1" max="20" class="mt-1 block w-full"
                :value="old('personas', $reserva?->personas)" required />
            <x-input-error :messages="$errors->get('personas')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="mesa_id" value="Mesa" />
        <select id="mesa_id" name="mesa_id" required
            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
            <option value="">Elige una mesa</option>
            @foreach ($mesas as $mesa)
                <option value="{{ $mesa->id }}" @selected(old('mesa_id', $reserva?->mesa_id) == $mesa->id)>
                    Mesa {{ $mesa->numero }} ({{ $mesa->capacidad }} personas)
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('mesa_id')" class="mt-2" />
    </div>

    {{-- El estado solo se elige al editar; al crear siempre es "pendiente" --}}
    @if ($reserva)
        <div>
            <x-input-label for="estado" value="Estado" />
            <select id="estado" name="estado"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                @foreach (\App\Models\Reserva::ESTADOS as $estado)
                    <option value="{{ $estado }}" @selected(old('estado', $reserva->estado) === $estado)>
                        {{ ucfirst($estado) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('estado')" class="mt-2" />
        </div>
    @endif
</div>
