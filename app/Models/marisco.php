<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class marisco extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion', 'origen', 'precioKG', 'cantidad', 'categoria','cocido', 'fechaCompra', 'imagen'];
}
