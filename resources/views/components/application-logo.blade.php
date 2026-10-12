{{-- Logo de Mesa Libre: mesa redonda vista desde arriba con cuatro sillas.
     $attributes pasa las clases que recibe el componente (tamaño y color),
     y como usa el color actual, cambia de color según dónde se ponga. --}}
<svg viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <circle cx="32" cy="32" r="14" />
    <rect x="24" y="4" width="16" height="8" rx="4" />
    <rect x="24" y="52" width="16" height="8" rx="4" />
    <rect x="4" y="24" width="8" height="16" rx="4" />
    <rect x="52" y="24" width="8" height="16" rx="4" />
</svg>
