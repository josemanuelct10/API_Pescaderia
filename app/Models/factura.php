<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class factura extends Model
{
    use HasFactory;
    protected $fillable = ['referencia','fecha', 'precioFactura', 'horaRecogida', 'metodoPago', 'documento', 'user_id'];


    // Relación de pertenencia a usuario (cada factura pertenece a un usuario)
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // Relación uno a muchos con las líneas de factura (cada factura puede tener varias líneas)
    public function lineas(): HasMany {
        return $this->hasMany(linea::class);
    }

}
