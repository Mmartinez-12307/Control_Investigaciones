<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;

class DocumentoController extends Controller
{
    public function show($id)
    {
        $documento = Documento::with('versiones')->findOrFail($id);

        return view('coordinador.versiones', compact('documento'));
    }
}
