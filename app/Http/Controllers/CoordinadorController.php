<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investigacion;
use App\Models\Documento;
use App\Models\DocumentoVersion;
use App\Helpers\AuthFake;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\ObservacionesMail;

class CoordinadorController extends Controller
{
    public function actualizarEstado(Request $request, $versionId)
    {
        $version = DocumentoVersion::findOrFail($versionId);
        $version->Estado = $request->Estado;
        $version->save();

        $documento = Documento::findOrFail($version->IdDocumento);
        $investigacion = Investigacion::findOrFail($documento->IdInvestigacion);

        $usuario = DB::table('Usuario')
            ->where('Carnet', $investigacion->Carnet)
            ->first();

        if ($usuario && $usuario->correo) {
            try {
                if ($request->Estado === 'Completado') {
                    $asunto = 'Documento aprobado';
                    $mensaje = 'Tu documento "' . $documento->Nombre . '" ha sido aprobado correctamente.';
                } elseif ($request->Estado === 'Pendiente_Nueva_Version') {
                    $asunto = 'Documento requiere correcciones';
                    $mensaje = 'Tu documento "' . $documento->Nombre . '" necesita correcciones. Revisa los comentarios del coordinador.';
                } else {
                    $asunto = 'Actualización de documento';
                    $mensaje = 'El estado de tu documento fue actualizado.';
                }

                Mail::to($usuario->correo)->send(
                    new ObservacionesMail($asunto, $mensaje, $documento->Nombre)
                );

                return redirect()->back()->with([
                    'success' => 'Estado actualizado correctamente.',
                    'correo_ok' => 'Correo enviado a ' . $usuario->correo,
                ]);
            } catch (\Exception $e) {
                Log::error($e->getMessage());

                return redirect()->back()->with([
                    'success' => 'Estado actualizado correctamente.',
                    'correo_error' => 'El correo no se pudo enviar.',
                ]);
            }
        } else {
            Log::error('No se encontró correo del usuario');

            return redirect()->back()->with([
                'success' => 'Estado actualizado correctamente.',
                'correo_error' => 'No se encontró correo del usuario.',
            ]);
        }
    }
}
