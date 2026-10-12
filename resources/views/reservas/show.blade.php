<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reserva de {{ $reserva->nombre_cliente }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <dt class="text-gray-500">Cliente</dt>   <dd>{{ $reserva->nombre_cliente }}</dd>
                    <dt class="text-gray-500">Teléfono</dt>  <dd>{{ $reserva->telefono }}</dd>
                    <dt class="text-gray-500">Fecha</dt>     <dd>{{ $reserva->fecha->format('d/m/Y') }}</dd>
                    <dt class="text-gray-500">Hora</dt>      <dd>{{ substr($reserva->hora, 0, 5) }}</dd>
                    <dt class="text-gray-500">Personas</dt>  <dd>{{ $reserva->personas }}</dd>
                    <dt class="text-gray-500">Mesa</dt>      <dd>Mesa {{ $reserva->mesa->numero }} ({{ $reserva->mesa->capacidad }} personas)</dd>
                    <dt class="text-gray-500">Estado</dt>    <dd>@include('reservas._estado')</dd>
                </dl>

                <div class="flex items-center gap-4 mt-6">
                    <a href="{{ route('reservas.edit', $reserva) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Editar</a>
                    @if ($reserva->estado !== 'cancelada')
                        <form method="POST" action="{{ route('reservas.cancelar', $reserva) }}"
                            onsubmit="return confirm('¿Cancelar esta reserva?')">
                            @csrf
                            @method('PATCH')
                            <x-danger-button>Cancelar reserva</x-danger-button>
                        </form>
                    @endif
                    <a href="{{ route('reservas.index', ['fecha' => $reserva->fecha->toDateString()]) }}"
                        class="text-sm text-gray-600 hover:underline">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
