<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Nueva reserva</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('reservas.store') }}">
                    @csrf
                    @include('reservas._form')

                    <div class="flex items-center gap-4 mt-6">
                        <x-primary-button>Guardar reserva</x-primary-button>
                        <a href="{{ route('reservas.index') }}" class="text-sm text-gray-600 hover:underline">Volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
