<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class categoriaUsuario extends Model
{

    protected $guarded = [];

    use HasFactory;

        // Relación uno a muchos con la tabla de usuarios
        public function users(): HasMany {
            return $this->hasMany(User::class);
        }
}
