@foreach ($factura->lineas as $linea)
    <!-- Acceder a los atributos de la línea de factura -->
    {{ $linea->descripcion }}
    {{ $linea->cantidad }}
    <!-- y así sucesivamente -->
@endforeach
