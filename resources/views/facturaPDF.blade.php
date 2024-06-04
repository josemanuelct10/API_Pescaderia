<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura de Pescadería</title>
    <style>

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            width: 100%;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
        }
        .header h1 {
            background-color: #052659;
            color: white;
            margin: 0;
        }
        .company-info,
        .customer-info {
            margin-bottom: 20px;
        }
        .company-info p,
        .customer-info p {
            margin: 5px 0;
        }
        .invoice-details,
        .invoice-items,
        .invoice-total {
            width: 100%;
            margin-bottom: 20px;
        }
        .invoice-details,
        .invoice-items,
        .invoice-total {
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .invoice-items table {
            width: 100%;
            border-collapse: collapse;
        }
        .invoice-items th,
        .invoice-items td {
            padding: 8px;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }
        .total-row {
            font-weight: bold;
            text-align: right;
        }
        .separator {
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Pescados Cañete Trillo</h1>
        </div>
        <div class="company-info">
            <p><strong>Capitán Ignacio de la Moneda 11 2C</strong></p>
            <p><strong>B14432678</strong></p>
        </div>
        <div class="customer-info">
            <p><strong>Cliente:</strong> {{ $factura->user->name }}</p>
            <p><strong>DNI/CIF:</strong> {{ $factura->user->dni }}</p>
            <p><strong>Dirección:</strong> {{ $factura->user->direccion }}</p>
            <p><strong>Teléfono:</strong> {{ $factura->user->telefono }}</p>
        </div>
        <div class="invoice-details">
            <p><strong>Fecha:</strong> {{ $factura->fecha }}</p>
            <p><strong>Número de factura:</strong> {{ $factura->id }}</p>
        </div>
        <div class="invoice-items">
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Preparación
                        <th>Precio por kg</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($factura->lineas as $linea)
                    <tr>
                        <td>
                            @if ($linea->pescado)
                                {{ $linea->pescado->nombre }}
                            @elseif ($linea->marisco)
                                {{ $linea->marisco->nombre }}
                            @else
                                Sin información
                            @endif
                        </td>
                        <td>{{ $linea->descripcion}}</td>
                        <td class="price">{{ number_format($linea->cantidad, 2) }} KG</td>
                        <td class="price">{{ number_format($linea->precioUnitario, 2) }} €</td>
                        <td class="price">{{ number_format($linea->precioLinea, 2) }} €</td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="invoice-total">
            @php
                $iva = $factura->precioFactura * 0.16;
                $subtotal = $factura->precioFactura - $iva;
            @endphp
            <div class="total-row">
                <p><strong>Subtotal:</strong> <span class="price">{{ number_format($subtotal, 2) }} €</span></p>
                <p><strong>IVA (16%):</strong> <span class="price">{{ number_format($iva, 2) }} €</span></p>
                <p><strong>Total:</strong> <span class="price">{{ number_format($factura->precioFactura, 2) }} €</span></p>
            </div>
        </div>
    </div>
</body>
</html>
