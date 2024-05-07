<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class gasto extends Model
{
    use HasFactory;
    protected $fillable = ['referencia','descripcion', 'cantidad', 'documento', 'fecha', 'user_id', 'proveedor_id'];

        // Relación de pertenencia a proveedor (cada pescado pertenece a un proveedor)
        public function proveedor(): BelongsTo {
            return $this->belongsTo(Proveedor::class);

        }

        // Relación de pertenencia a usuario (cada pescado pertenece a un usuario)
        public function user(): BelongsTo {
            return $this->belongsTo(User::class);
        }

}
