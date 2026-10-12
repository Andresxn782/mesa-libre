<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Plano de la sala</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Elegir el momento que se quiere ver --}}
            <form method="GET" action="{{ route('plano') }}"
                class="bg-white p-4 shadow-sm sm:rounded-lg flex flex-wrap items-center gap-3">
                <input type="date" name="fecha" value="{{ $fecha }}"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <input type="time" name="hora" value="{{ $hora }}"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <x-primary-button>Ver</x-primary-button>
                <a href="{{ route('plano') }}" class="text-sm text-gray-600 hover:underline">Ahora</a>

                {{-- Leyenda de colores --}}
                <div class="ml-auto flex gap-4 text-sm text-gray-600">
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-500"></span> Libre</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-amber-400"></span> Reservada</span>
                    <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-500"></span> Ocupada</span>
                </div>
            </form>

            {{-- La cuadrícula: 2 columnas en móvil, 4 en pantallas grandes --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach ($plano as $celda)
                    @php
                        // Libre → formulario nuevo con mesa, fecha y hora ya elegidas.
                        // Ocupada o reservada → detalle de esa reserva.
                        $enlace = $celda['estado'] === 'libre'
                            ? route('reservas.create', ['mesa_id' => $celda['mesa']->id, 'fecha' => $fecha, 'hora' => $hora])
                            : route('reservas.show', $celda['reserva']);
                    @endphp

                    {{-- Las clases de color se escriben completas (no "bg-{$color}-100")
                         porque Tailwind solo genera las clases que encuentra literalmente en el código --}}
                    <a href="{{ $enlace }}" @class([
                        'block p-5 rounded-xl border-2 shadow-sm transition hover:shadow-md',
                        'bg-green-50 border-green-500' => $celda['estado'] === 'libre',
                        'bg-amber-50 border-amber-400' => $celda['estado'] === 'reservada',
                        'bg-red-50 border-red-500'     => $celda['estado'] === 'ocupada',
                    ])>
                        <div class="flex justify-between items-start">
                            <span class="text-3xl font-bold text-gray-800">{{ $celda['mesa']->numero }}</span>
                            <span class="text-sm text-gray-500">👥 {{ $celda['mesa']->capacidad }}</span>
                        </div>

                        <p class="mt-3 text-sm font-semibold uppercase tracking-wide text-gray-700">{{ $celda['estado'] }}</p>

                        <p class="mt-1 text-sm text-gray-600 truncate">
                            @if ($celda['estado'] === 'ocupada')
                                {{ $celda['reserva']->nombre_cliente }} · hasta {{ $celda['fin']->format('H:i') }}
                            @elseif ($celda['estado'] === 'reservada')
                                {{ substr($celda['reserva']->hora, 0, 5) }} · {{ $celda['reserva']->nombre_cliente }}
                            @else
                                Pulsa para reservar
                            @endif
                        </p>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
