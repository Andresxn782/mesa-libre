<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reservas</h2>
            <a href="{{ route('reservas.create', ['fecha' => $fecha]) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                + Nueva reserva
            </a>
        </div>
    </x-slot>

    @php($dia = \Illuminate\Support\Carbon::parse($fecha))

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Mensaje flash: se guarda en sesión al redirigir y se muestra una sola vez --}}
            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            {{-- Filtro: formulario GET, así la fecha va en la URL (?fecha=...) y se puede compartir --}}
            <form method="GET" action="{{ route('reservas.index') }}"
                class="bg-white p-4 shadow-sm sm:rounded-lg flex flex-wrap items-center gap-3">
                <a href="{{ route('reservas.index', ['fecha' => $dia->copy()->subDay()->toDateString()]) }}"
                    class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">←</a>
                <input type="date" name="fecha" value="{{ $fecha }}"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                <a href="{{ route('reservas.index', ['fecha' => $dia->copy()->addDay()->toDateString()]) }}"
                    class="px-3 py-2 border border-gray-300 rounded-md hover:bg-gray-50">→</a>
                <x-primary-button>Filtrar</x-primary-button>
                <a href="{{ route('reservas.index') }}" class="text-sm text-gray-600 hover:underline">Hoy</a>
                <span class="ml-auto text-sm text-gray-600">
                    {{ $dia->format('d/m/Y') }} · {{ $reservas->where('estado', '!=', 'cancelada')->count() }} reservas activas
                </span>
            </form>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                @if ($reservas->isEmpty())
                    <p class="p-6 text-gray-500">No hay reservas para este día.</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="px-4 py-3">Hora</th>
                                <th class="px-4 py-3">Mesa</th>
                                <th class="px-4 py-3">Cliente</th>
                                <th class="px-4 py-3">Teléfono</th>
                                <th class="px-4 py-3">Personas</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($reservas as $reserva)
                                <tr @class(['text-gray-400' => $reserva->estado === 'cancelada'])>
                                    <td class="px-4 py-3 font-semibold">{{ substr($reserva->hora, 0, 5) }}</td>
                                    <td class="px-4 py-3">Mesa {{ $reserva->mesa->numero }}</td>
                                    <td class="px-4 py-3">{{ $reserva->nombre_cliente }}</td>
                                    <td class="px-4 py-3">{{ $reserva->telefono }}</td>
                                    <td class="px-4 py-3">{{ $reserva->personas }}</td>
                                    <td class="px-4 py-3">@include('reservas._estado')</td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap space-x-2">
                                        <a href="{{ route('reservas.show', $reserva) }}" class="text-indigo-600 hover:underline">Ver</a>
                                        <a href="{{ route('reservas.edit', $reserva) }}" class="text-indigo-600 hover:underline">Editar</a>
                                        @if ($reserva->estado !== 'cancelada')
                                            <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}" class="inline"
                                                onsubmit="return confirm('¿Cancelar esta reserva?')">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-red-600 hover:underline">Cancelar</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
