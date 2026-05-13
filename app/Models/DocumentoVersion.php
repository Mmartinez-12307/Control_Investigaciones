<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Documento;
use App\Models\Usuario;

class DocumentoVersion extends Model
{
    use HasFactory;

    protected $table = 'Documento_Version';
    protected $primaryKey = 'IdVersion';
    public $timestamps = false;

    protected $fillable = [
        'IdDocumento',
        'NumeroVersion',
        'RutaArchivo',
        'Comentario',
        'Estado',
        'Fecha',
        'IdUsuario',
    ];

    protected $casts = [
        'Fecha' => 'datetime',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'IdDocumento');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'IdUsuario');
    }

    public function versiones()
    {
        return $this->hasMany(DocumentoVersion::class, 'IdDocumento');
    }
}
