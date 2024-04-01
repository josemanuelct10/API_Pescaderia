<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class pescado extends Model
{
    use HasFactory;
    protected $fillable = ['nombre', 'descripcion', 'origen', 'precioKG', 'cantidad', 'fechaCompra','categoria', 'imagen'];

}
