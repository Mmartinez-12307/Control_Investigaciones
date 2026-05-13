<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investigacion extends Model
{
    use HasFactory;

    protected $table = 'Investigacion';
    protected $primaryKey = 'IdInvestigacion';
    public $timestamps = false;

    protected $fillable = [
        'Titulo',
        'Descripcion',
        'FechaCreacion',
        'Estado',
        'IdUsuario',
        'IdEscuela',
        'Carrera',
        'Materia',
        'Seccion',
        'Carnet',
    ];

    protected $casts = [
        'FechaCreacion' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'IdUsuario');
    }

    public function escuela()
    {
        return $this->belongsTo(Escuela::class, 'IdEscuela');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'IdInvestigacion');
    }
    public function docente()
{
    return $this->belongsTo(Usuario::class, 'Carnet', 'Carnet');
}
}

