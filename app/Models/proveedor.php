<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class proveedor extends Model
{
    // Se especifica la tabla asociada al modelo
    protected $table = 'proveedores';
    use HasFactory;
    protected $fillable = ['nombre', 'direccion', 'telefono', 'categoria', 'cif' ];


    // Relación uno a muchos con los pescados (un proveedor puede tener varios pescados)
    public function pescados(): HasMany{
        return $this->hasMany(Pescado::class);
    }

    // Relación uno a muchos con los mariscos (un proveedor puede tener varios mariscos)
    public function mariscos(): HasMany{
        return $this->hasMany(Marisco::class);
    }

    // Relacion uno a muchos con gastos (un proveedor puede tener uno o muchos gastos)
    public function gastos(): HasMany {
        return $this->hasMany(Gasto::class);
    }
}
