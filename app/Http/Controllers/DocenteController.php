<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Investigacion;
use App\Models\Documento;
use App\Models\DocumentoVersion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ObservacionesMail;

class DocenteController extends Controller
{
    public function index()
    {
        $docente = Auth::user();
        $carnetDocente = $docente->Carnet;

        // Estadísticas del docente

        $investigaciones = Investigacion::where('Carnet', $carnetDocente)->get();

        $investigacionSeleccionada = $investigaciones->first()?->IdInvestigacion;

        $queryBase = DocumentoVersion::whereHas('documento.investigacion', function ($q) use ($carnetDocente, $investigacionSeleccionada) {
            $q->where('Carnet', $carnetDocente);

            if ($investigacionSeleccionada) {
                $q->where('IdInvestigacion', $investigacionSeleccionada);
            }
        });

        $totalDocumentos = (clone $queryBase)->count();

        $pendientes = (clone $queryBase)
            ->where('Estado', 'Pendiente')
            ->count();

        $completados = (clone $queryBase)
            ->where('Estado', 'Completado')
            ->count();

        $pendienteNuevaVersion = (clone $queryBase)
            ->where('Estado', 'Pendiente_Nueva_Version')
            ->count();

        // Historial reciente (últimos 10)
        $historial = DocumentoVersion::whereHas('documento.investigacion', function ($q) use ($carnetDocente) {
            $q->where('carnet', $carnetDocente);
        })
            ->with('documento')
            ->orderBy('Fecha', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($version) {
                return [
                    'Nombre' => $version->documento->Nombre ?? 'Sin nombre',
                    'tipo_entrega' => $version->documento->tipo_entrega ?? 'avance_1',
                    'NumeroVersion' => $version->NumeroVersion,
                    'Fecha' => $version->Fecha,
                    'Estado' => $version->Estado ?? 'Pendiente',
                    'tipo_entrega_text' => match ($version->documento->tipo_entrega ?? 'avance_1') {
                        'avance_1' => 'Avance 1',
                        'avance_2' => 'Avance 2',
                        'avance_3' => 'Avance 3',
                        'final' => 'Documento Final',
                        'banner' => 'Banner',
                        'extra' => 'Documento Extra',
                        default => 'Otro'
                    },

                    'EstadoTexto' => match ($version->Estado) {
                        'Pendiente' => 'Pendiente',
                        'Pendiente_Nueva_Version' => 'Pendiente Nueva Versión',
                        'Completado' => 'Completado',
                        'Corregido' => 'Corregido',
                        default => 'Desconocido'
                    },
                ];
            });

        return view('docente.index', compact(
            'docente',
            'totalDocumentos',
            'pendienteNuevaVersion',
            'pendientes',
            'completados',
            'historial',
            'investigaciones'
        ));
    }

    public function dashboardData($id = null)
    {
        $docente = Auth::user();
        $carnetDocente = $docente->Carnet;

        $queryBase = DocumentoVersion::whereHas('documento.investigacion', function ($q) use ($carnetDocente, $id) {
            $q->where('Carnet', $carnetDocente);

            if ($id) {
                $q->where('IdInvestigacion', $id);
            }
        });

        $pendienteNuevaVersion = (clone $queryBase)->where('Estado', 'Pendiente_Nueva_Version')->count();
        $pendientes = (clone $queryBase)->where('Estado', 'Pendiente')->count();
        $revisados = (clone $queryBase)->where('Estado', 'Revisado')->count();
        $completados = (clone $queryBase)->whereIn('Estado', ['Completado', 'Aprobado'])->count();
        $totalDocumentos = (clone $queryBase)->count();

        return response()->json([
            'pendiente_nueva_version' => $pendienteNuevaVersion,
            'pendientes' => $pendientes,
            'revisados' => $revisados,
            'completados' => $completados,
            'total_documentos' => $totalDocumentos,
        ]);
    }

    public function investigaciones()
    {
        $docente = Auth::user();

        $investigaciones = Investigacion::with('escuela')
            ->where('Carnet', $docente->Carnet)
            ->orderBy('FechaCreacion', 'desc')
            ->get();

        return view('docente.investigaciones.index', compact('investigaciones', 'docente'));
    }

    // Muestra el formulario para subir documentos a una investigación
    public function subirDocumentos($id)
    {
        $docente = Auth::user();

        $investigacion = Investigacion::with('escuela')
            ->findOrFail($id);

        return view('docente.documentos.create', compact('investigacion', 'docente'));
    }

    public function storeDocumento(Request $request)
    {
        $docenteId = Auth::id();

        try {
            $request->validate([
                'IdInvestigacion' => 'required|exists:investigacion,IdInvestigacion',
                'tipo_entrega' => 'required|in:avance_1,avance_2,avance_3,final,banner,extra',
                'archivos' => 'required|array|min:1',
                'archivos.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:35840',
                'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            $IdInvestigacion = $request->IdInvestigacion;
            $tipoEntrega = $request->tipo_entrega;
            $comentarioInicial = $request->Comentario ?? 'Versión inicial';

            $rutaCarpeta = 'uploads/documentos/' . $IdInvestigacion;

            if (!file_exists(public_path($rutaCarpeta))) {
                mkdir(public_path($rutaCarpeta), 0777, true);
            }
            /*
        SUBIR DOCUMENTO PRINCIPAL
        */
            foreach ($request->file('archivos') as $archivo) {

                $nombreOriginal = $archivo->getClientOriginalName();
                $nombreArchivo = time() . '_' . $nombreOriginal;

                $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

                $rutaFinal = $rutaCarpeta . '/' . $nombreArchivo;

                $documento = Documento::create([
                    'Nombre' => pathinfo($nombreOriginal, PATHINFO_FILENAME),
                    'Fecha' => now(),
                    'IdInvestigacion' => $IdInvestigacion,
                    'tipo_entrega' => $tipoEntrega,
                ]);

                $estado = $tipoEntrega === 'extra' ? 'Completado' : 'Pendiente';

                DocumentoVersion::create([
                    'IdDocumento' => $documento->IdDocumento,
                    'NumeroVersion' => 1,
                    'RutaArchivo' => $rutaFinal,
                    'Comentario' => $comentarioInicial,
                    'Estado' => $estado,
                    'Fecha' => now(),
                    'IdUsuario' => $docenteId,
                ]);
            }

            /*
        DOCUMENTO FINAL + BANNER
        */
            if ($tipoEntrega === 'final' && $request->hasFile('banner')) {

                $banner = $request->file('banner');

                $nombreOriginalBanner = $banner->getClientOriginalName();
                $nombreBanner = time() . '_banner_' . $nombreOriginalBanner;

                $banner->move(public_path($rutaCarpeta), $nombreBanner);

                $rutaBanner = $rutaCarpeta . '/' . $nombreBanner;

                $documentoBanner = Documento::create([
                    'Nombre' => pathinfo($nombreOriginalBanner, PATHINFO_FILENAME),
                    'Fecha' => now(),
                    'IdInvestigacion' => $IdInvestigacion,
                    'tipo_entrega' => 'banner',
                ]);

                DocumentoVersion::create([
                    'IdDocumento' => $documentoBanner->IdDocumento,
                    'NumeroVersion' => 1,
                    'RutaArchivo' => $rutaBanner,
                    'Comentario' => 'Banner del documento final',
                    'Estado' => 'Pendiente',
                    'Fecha' => now(),
                    'IdUsuario' => $docenteId,
                ]);
            }

            return redirect()->back()->with(
                'success',
                'Documento(s) subido(s) correctamente.'
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al subir el documento.');
        }
    }

    // lista de documentos subidos
    public function verDocumentos($id)
    {
        $docente = Auth::user();

        $investigacion = Investigacion::with('escuela')
            ->where('Carnet', $docente->Carnet)
            ->findOrFail($id);

        $documentos = Documento::where('IdInvestigacion', $id)
            ->with(['versions' => function ($query) {
                $query->orderBy('NumeroVersion', 'desc');
            }])
            ->orderBy('Fecha', 'desc')
            ->get();

        return view('docente.documentos.index', compact('investigacion', 'documentos'));
    }

    public function revision($id)
    {
        $docente = Auth::user();

        $investigacion = Investigacion::with('escuela')
            ->where('Carnet', $docente->Carnet)
            ->findOrFail($id);

        $documentos = Documento::where('IdInvestigacion', $id)
            ->with(['versions' => function ($query) {
                $query->orderBy('NumeroVersion', 'desc');
            }])
            ->orderBy('Fecha', 'desc')
            ->get();

        return view('docente.revision.index', compact('investigacion', 'documentos'));
    }

    //Descargar un archivo
    public function descargarDocumento($id)
    {
        $version = DocumentoVersion::findOrFail($id);

        $rutaArchivo = public_path($version->RutaArchivo);

        if (!file_exists($rutaArchivo)) {
            abort(404, 'Archivo no encontrado');
        }

        return response()->download(
            $rutaArchivo,
            $version->documento->Nombre . '.' . pathinfo($rutaArchivo, PATHINFO_EXTENSION)
        );
    }

    // Eliminar un documento (y todas sus versiones)
    public function eliminarDocumento($idVersion)
    {
        $version = DocumentoVersion::findOrFail($idVersion);
        $documento = Documento::findOrFail($version->IdDocumento);

        $documentoId = $documento->IdDocumento;
        $investigacionId = $documento->IdInvestigacion;

        // Eliminar archivo físico
        $rutaArchivo = public_path($version->RutaArchivo);
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }

        // Eliminar versión
        $version->delete();

        // Verificar si quedan versiones
        $versionRestante = DocumentoVersion::where('IdDocumento', $documentoId)
            ->orderBy('NumeroVersion', 'desc')
            ->first();

        if ($versionRestante) {
            $versionRestante->Estado = 'Pendiente_Nueva_Version';
            $versionRestante->save();

            return redirect()
                ->route('docente.investigaciones.documentos', $investigacionId)
                ->with('success', 'Versión eliminada correctamente.');
        } else {
            $documento->delete();

            return redirect()
                ->route('docente.investigaciones.documentos', $investigacionId)
                ->with('success', 'Documento eliminado completamente.');
        }
    }

    /**
     * formulario para subir una nueva versión de un documento
     */
    public function nuevaVersionForm($documentoId)
    {
        $documento = Documento::with('investigacion')->findOrFail($documentoId);

        // Obtener el número de la siguiente versión
        $ultimaVersion = DocumentoVersion::where('IdDocumento', $documentoId)
            ->max('NumeroVersion');

        $siguienteVersion = $ultimaVersion ? $ultimaVersion + 1 : 1;

        return view('docente.documentos.nueva-version', compact('documento', 'siguienteVersion'));
    }

    /**
     * guarda la nueva versión del documento
     */
    public function storeNuevaVersion(Request $request)
    {
        $docenteId = Auth::id();

        $request->validate([
            'IdDocumento' => 'required|exists:documento,IdDocumento',
            'archivos' => 'required|array|min:1',
            'archivos.*' => 'file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:35840',
        ]);

        $IdDocumento = $request->IdDocumento;
        $comentario = $request->Comentario ?? 'Corrección de versión';

        // Obtener el documento y la última versión
        $documento = Documento::findOrFail($IdDocumento);
        $ultimaVersion = DocumentoVersion::where('IdDocumento', $IdDocumento)
            ->max('NumeroVersion') ?? 0;

        $nuevaVersion = $ultimaVersion + 1;

        // Carpeta
        $rutaCarpeta = 'uploads/documentos/' . $documento->IdInvestigacion;
        if (!file_exists(public_path($rutaCarpeta))) {
            mkdir(public_path($rutaCarpeta), 0777, true);
        }

        foreach ($request->file('archivos') as $archivo) {
            $nombreOriginal = $archivo->getClientOriginalName();
            $nombreArchivo = time() . '_' . $nombreOriginal;
            $rutaFinal = $rutaCarpeta . '/' . $nombreArchivo;

            $archivo->move(public_path($rutaCarpeta), $nombreArchivo);

            // actualizar nombre visible del documento
            $documento->Nombre = $nombreOriginal;
            $documento->save();

            DocumentoVersion::create([
                'IdDocumento'   => $IdDocumento,
                'NumeroVersion' => $nuevaVersion,
                'RutaArchivo'   => $rutaFinal,
                'Comentario'    => $comentario,
                'Estado'        => 'Pendiente',
                'Fecha'         => now(),
                'IdUsuario'     => $docenteId,
            ]);
        }

        $documento->Fecha = now();
        $documento->save();

        // Cambiar estado de versiones anteriores a "Corregido"
        DocumentoVersion::where('IdDocumento', $IdDocumento)
            ->where('NumeroVersion', '<', $nuevaVersion)
            ->update(['Estado' => 'Corregido']);

        return redirect()
            ->route('docente.investigaciones.revision', $documento->IdInvestigacion)
            ->with('success', 'Nueva versión v' . $nuevaVersion . ' subida correctamente.');
    }

    public function historial(Request $request)
    {
        $docente = Auth::user();
        $carnetDocente = $docente->Carnet;

        $filtro = $request->tipo_entrega;

        $query = Documento::whereHas('investigacion', function ($q) use ($carnetDocente) {
            $q->where('carnet', $carnetDocente);
        })->with(['versions' => function ($q) {
            $q->orderBy('Fecha', 'desc');
        }]);

        if ($filtro && $filtro != 'todos') {
            $query->where('tipo_entrega', $filtro);
        }

        $historial = $query
            ->get()
            ->sortByDesc(function ($documento) {
                return optional($documento->versions->first())->Fecha;
            });

        return view('docente.historial', compact('historial', 'filtro'));
    }
}
