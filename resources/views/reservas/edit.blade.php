<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Editar reserva</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('reservas.update', $reserva) }}">
                    @csrf
                    {{-- Los formularios HTML solo admiten GET y POST; esto le dice a Laravel que es un PUT --}}
                    @method('PUT')
                    @include('reservas._form')

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>Guardar cambios</x-primary-button>
                        <a href="{{ route('reservas.index', ['fecha' => $reserva->fecha->toDateString()]) }}"
                            class="text-sm text-gray-600 hover:underline">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
