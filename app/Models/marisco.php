<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class marisco extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'origen', 'precioKG', 'cantidad', 'categoria','cocido', 'fechaCompra', 'imagen', 'user_id', 'proveedor_id'];

        // Relación de pertenencia a usuario (cada marisco pertenece a un usuario)
        public function user(): BelongsTo {
            return $this->belongsTo(User::class);
        }

        // Relación de pertenencia a proveedor (cada marisco pertenece a un proveedor)
        public function proveedor(): BelongsTo {
            return $this->belongsTo(Proveedor::class);
        }

        // Relación uno a muchos con las líneas de factura (cada pescado puede estar en varias líneas de factura)
        public function lineas(): HasMany {
            return $this->hasMany(Linea::class);
        }
}
