<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escuela extends Model
{
    use HasFactory;
    protected $table = 'Escuela';

    protected $primaryKey = 'IdEscuela';

    public $timestamps = false;

    protected $fillable = [
        'Nombre'
    ];

    public function investigaciones()
    {
        return $this->hasMany(Investigacion::class, 'IdEscuela', 'IdEscuela');
    }
}


