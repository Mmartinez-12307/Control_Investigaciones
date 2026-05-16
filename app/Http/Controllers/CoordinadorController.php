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
        try {
            $version = DocumentoVersion::findOrFail($versionId);

            $nuevoEstado = $request->Estado;

            $version->Estado = $nuevoEstado;
            $version->save();

            $documento = Documento::findOrFail($version->IdDocumento);

            $investigacion = Investigacion::findOrFail($documento->IdInvestigacion);

            $usuario = DB::table('Usuario')
                ->where('Carnet', $investigacion->carnet)
                ->first();

            /* if ($usuario && $usuario->Correo) {

                if ($nuevoEstado === 'Completado') {
                    $asunto = 'Documento aprobado';
                    $mensaje = 'Tu documento "' . $documento->Nombre . '" ha sido aprobado correctamente.';
                } elseif ($nuevoEstado === 'Pendiente_Nueva_Version') {
                    $asunto = 'Documento requiere correcciones';
                    $mensaje = 'Tu documento "' . $documento->Nombre . '" necesita correcciones. Revisa los comentarios del coordinador.';
                } else {
                    $asunto = 'Actualización de documento';
                    $mensaje = 'El estado de tu documento fue actualizado.';
                }

                Mail::to($usuario->Correo)->send(
                    new ObservacionesMail($asunto, $mensaje, $documento->Nombre)
                );
            }
                */

            if ($usuario && $usuario->correo) {

                Log::info('Usuario encontrado: ' . $usuario->correo);

                if ($nuevoEstado === 'Completado') {
                    $asunto = 'Documento aprobado';
                    $mensaje = 'Tu documento "' . $documento->Nombre . '" ha sido aprobado correctamente.';
                } elseif ($nuevoEstado === 'Pendiente_Nueva_Version') {
                    $asunto = 'Documento requiere correcciones';
                    $mensaje = 'Tu documento "' . $documento->Nombre . '" necesita correcciones. Revisa los comentarios del coordinador.';
                } else {
                    $asunto = 'Actualización de documento';
                    $mensaje = 'El estado de tu documento fue actualizado.';
                }

                Log::info('Intentando enviar correo a: ' . $usuario->correo);

                Mail::to($usuario->correo)->send(
                    new ObservacionesMail($asunto, $mensaje, $documento->Nombre)
                );

                Log::info('Correo enviado correctamente');
            } else {
                Log::error('No se encontró correo del usuario');
            }

            return redirect()->back()->with('success', 'Estado actualizado y correo enviado correctamente.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return redirect()->back()->with('error', 'Ocurrió un error al actualizar el estado.');
        }
    }
}
