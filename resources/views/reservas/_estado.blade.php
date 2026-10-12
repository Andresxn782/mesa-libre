{{-- Etiqueta de color según el estado. Recibe $reserva de la vista que la incluye. --}}
<span @class([
    'px-2 py-1 rounded-full text-xs font-semibold',
    'bg-yellow-100 text-yellow-800' => $reserva->estado === 'pendiente',
    'bg-green-100 text-green-800'   => $reserva->estado === 'confirmada',
    'bg-gray-200 text-gray-600'     => $reserva->estado === 'cancelada',
])>
    {{ ucfirst($reserva->estado) }}
</span>
