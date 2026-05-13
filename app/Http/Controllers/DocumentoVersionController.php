<?php

namespace App\Http\Controllers;

use App\Models\DocumentoVersion;
use Illuminate\Http\Request;

class DocumentoVersionController extends Controller
{
    public function show($id)
    {
        $version = DocumentoVersion::findOrFail($id);
        return view('coordinador.version', compact('version'));
    }

    public function updateEstado(Request $request, $id)
    {
        $request->validate([
            'Estado' => 'required'
        ]);
        $version = DocumentoVersion::findOrFail($id);
        $version->Estado = $request->Estado;
        $version->save();
        return back()->with('success', 'Estado actualizado correctamente');
    }

    public function updateComentario(Request $request, $id)
    {
        $request->validate([
            'Comentario' => 'required'
        ]);
        $version = DocumentoVersion::findOrFail($id);
        $version->Comentario = $request->Comentario;
        $version->save();
        return back()->with('success', 'Comentario actualizado correctamente');
    }
}
