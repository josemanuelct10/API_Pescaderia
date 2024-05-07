<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class linea extends Model
{
    use HasFactory;
    protected $fillable = [
        'descripcion',
        'cantidad',
        'precioLinea',
        'precioUnitario',
        'preparacion',
        'factura_id',
        'pescado_id',
        'marisco_id'
        ];

    // Relación de pertenencia a factura (cada línea pertenece a una factura)
    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    // Relación de pertenencia a pescado (cada línea pertenece a un pescado)
    public function pescado(): BelongsTo{
        return $this->belongsTo(Pescado::class);
    }

    // Relación de pertenencia a marisco (cada línea pertenece a un marisco)
    public function marisco(): BelongsTo{
        return $this->belongsTo(Marisco::class);
    }


}
