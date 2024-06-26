<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dni',
        'fecha_nacimiento',
        'telefono',
        'direccion',
        'categoria_usuario_id',
        'reset_password_token',
        'reset_password_token_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }



    // Relación de pertenencia a categoría de usuario (cada usuario pertenece a una categoría de usuario)
    public function categoriaUsuario(): BelongsTo {
        return $this->belongsTo(categoriaUsuario::class);
    }

    // Relación uno a muchos con mariscos (un usuario puede tener varios mariscos)
    public function mariscos(): HasMany {
        return $this->hasMany(Marisco::class);
    }

    // Relación uno a muchos con pescados (un usuario puede tener varios pescados)
    public function pescados(): HasMany {
        return $this->hasMany(Pescado::class);
    }

    // Relacion uno a muchos con gastos (un usuario puede tener uno o muchos gastos)
    public function gastos(): HasMany {
        return $this->hasMany(gasto::class);
    }

    // Relacion uno a muchos con facturas (un usuario puede tener una o muchas facturas)
    public function facturas(): HasMany {
        return $this->hasMany(factura::class);
    }

    // Un usuario solo puede tener un solo unico carrito
    public function carrito(): HasOne
    {
        return $this->hasOne(Carrito::class);
    }
}
