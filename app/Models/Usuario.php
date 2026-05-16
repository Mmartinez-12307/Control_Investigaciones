<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticable
{
    use HasFactory, Notifiable;
    protected $table = 'Usuario';

    protected $primaryKey = 'IdUsuario';

    public $timestamps = false;

    protected $fillable = [
        'Nombres',
        'Apellidos',
        'Carnet',
        'Clave',
        'IdRol',
        'IdEscuela',
        'Correo',
    ];

    protected $hidden = [
        'Clave',
    ];
    
    public function getAuthPassword()
    {
        return $this->Clave;
    }

    public function getAuthIdentifierName()
    {
        return 'IdUsuario';
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'IdRol');
    }

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'IdEscuela');
    }
}
