<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class carrito extends Model
{
    use HasFactory;
    protected $fillable = ['user_id'];

    // Relación de pertenencia a usuario (cada carrito pertenece a un usuario)
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // Relación uno a muchos con las líneas de factura (cada factura puede tener varias líneas)
    public function lineas(): HasMany {
        return $this->hasMany(Linea::class);
    }
}
