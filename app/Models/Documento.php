<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'Documento';
    protected $primaryKey = 'IdDocumento';
    public $timestamps = false;

    protected $fillable = [
        'Nombre',
        'Fecha',
        'IdInvestigacion',
        'tipo_entrega'
    ];

    protected $casts = [
        'Fecha' => 'date',
    ];

    public function investigacion()
    {
        return $this->belongsTo(Investigacion::class, 'IdInvestigacion', 'IdInvestigacion');
    }

    public function versions()
    {
        return $this->hasMany(DocumentoVersion::class, 'IdDocumento', 'IdDocumento');
    }

    public function versiones()
    {
        return $this->hasMany(DocumentoVersion::class, 'IdDocumento');
    }
}
