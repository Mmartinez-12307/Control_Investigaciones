<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Rol;
use Illuminate\Http\Request;
use App\Models\Escuela;
use App\Models\Investigacion;
use App\Models\Documento;
use App\Models\DocumentoVersion;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class DecanoController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Investigacion::query();

        if ($request->filled('fecha_inicio')) {
            $baseQuery->whereDate('FechaCreacion', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $baseQuery->whereDate('FechaCreacion', '<=', $request->fecha_fin);
        }

        $total = (clone $baseQuery)->count();
        $pendientes = (clone $baseQuery)->where('Estado', 'Pendiente')->count();
        $revision = (clone $baseQuery)->where('Estado', 'En revisión')->count();
        $completadas = (clone $baseQuery)->where('Estado', 'Completado')->count();

        $escuelas = Escuela::withCount('investigaciones')->get();

        $reporteEscuelas = Escuela::withCount(['investigaciones' => function ($query) use ($request) {
            if ($request->filled('fecha_inicio')) {
                $query->whereDate('FechaCreacion', '>=', $request->fecha_inicio);
            }

            if ($request->filled('fecha_fin')) {
                $query->whereDate('FechaCreacion', '<=', $request->fecha_fin);
            }
        }])->get();

        $reporteEstados = (clone $baseQuery)
            ->select('Estado', DB::raw('count(*) as total'))
            ->groupBy('Estado')
            ->get();

        $reporteDocentes = Usuario::where('IdRol', 3)
            ->withCount(['investigacionesAsignadas as total' => function ($query) use ($request) {
                if ($request->filled('fecha_inicio')) {
                    $query->whereDate('FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $query->whereDate('FechaCreacion', '<=', $request->fecha_fin);
                }
            }])
            ->orderBy('Nombres')
            ->get();

        $documentosQuery = Documento::query();

        if ($request->filled('fecha_inicio')) {
            $documentosQuery->whereDate('Fecha', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $documentosQuery->whereDate('Fecha', '<=', $request->fecha_fin);
        }

        $reporteDocumentos = $documentosQuery
            ->select('tipo_entrega', DB::raw('count(*) as total'))
            ->groupBy('tipo_entrega')
            ->get();

        return view('decano.index', compact(
            'escuelas',
            'total',
            'pendientes',
            'revision',
            'completadas',
            'reporteEscuelas',
            'reporteEstados',
            'reporteDocentes',
            'reporteDocumentos'
        ));
    }

    public function escuela($id)
    {
        $escuela = Escuela::findOrFail($id);

        $investigaciones = Investigacion::with('docente')
            ->where('IdEscuela', $id)
            ->orderBy('FechaCreacion', 'desc')
            ->get();

        return view('decano.investigaciones', compact('escuela', 'investigaciones'));
    }

    public function usuarios()
    {
        $usuarios = Usuario::with('rol', 'escuela')
            ->orderBy('IdRol')
            ->orderBy('Nombres')
            ->get();

        $roles = Rol::all();
        $escuelas = Escuela::all();

        return view('decano.usuarios', compact('usuarios', 'roles', 'escuelas'));
    }

    public function storeUsuario(Request $request)
    {
        $request->validate([
            'Nombres' => 'required',
            'Apellidos' => 'required',
            'Carnet' => 'required|unique:Usuario,Carnet',
            'correo' => 'nullable|email',
            'Clave' => 'required|min:6',
            'IdRol' => 'required',
            'IdEscuela' => 'required'
        ]);

        Usuario::create([
            'Nombres' => $request->Nombres,
            'Apellidos' => $request->Apellidos,
            'Carnet' => $request->Carnet,
            'correo' => $request->correo,
            'Clave' => bcrypt($request->Clave),
            'IdRol' => $request->IdRol,
            'IdEscuela' => $request->IdEscuela,
        ]);

        return back()->with('success', 'Usuario creado correctamente');
    }

    public function updateUsuario(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'Nombres' => 'required',
            'Apellidos' => 'required',
            'Carnet' => 'required|unique:Usuario,Carnet,' . $usuario->IdUsuario . ',IdUsuario',
            'correo' => 'nullable|email',
            'IdRol' => 'required',
            'IdEscuela' => 'required',
            'Clave' => 'nullable|min:6',
        ]);

        $usuario->Nombres = $request->Nombres;
        $usuario->Apellidos = $request->Apellidos;
        $usuario->Carnet = $request->Carnet;
        $usuario->correo = $request->correo;
        $usuario->IdRol = $request->IdRol;
        $usuario->IdEscuela = $request->IdEscuela;

        if ($request->filled('Clave')) {
            $usuario->Clave = bcrypt($request->Clave);
        }

        $usuario->save();

        return back()->with('success', 'Usuario actualizado correctamente');
    }

    public function showInvestigacion($id)
    {
        $investigacion = Investigacion::with('documentos.versiones', 'docente', 'escuela')
            ->findOrFail($id);

        return view('decano.show', compact('investigacion'));
    }

    public function showDocumento($id)
    {
        $documento = Documento::with('versiones', 'investigacion')
            ->findOrFail($id);

        return view('decano.versiones', compact('documento'));
    }

    public function showVersion($id)
    {
        $version = DocumentoVersion::with('documento', 'usuario')->findOrFail($id);

        return view('decano.version', compact('version'));
    }

    public function exportPdf(Request $request)
    {
        $baseQuery = Investigacion::query();

        if ($request->filled('fecha_inicio')) {
            $baseQuery->whereDate('FechaCreacion', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $baseQuery->whereDate('FechaCreacion', '<=', $request->fecha_fin);
        }

        $total = (clone $baseQuery)->count();
        $pendientes = (clone $baseQuery)->where('Estado', 'Pendiente')->count();
        $revision = (clone $baseQuery)->where('Estado', 'En revisión')->count();
        $completadas = (clone $baseQuery)->where('Estado', 'Completado')->count();

        $reporteEscuelas = Escuela::withCount(['investigaciones' => function ($query) use ($request) {
            if ($request->filled('fecha_inicio')) {
                $query->whereDate('FechaCreacion', '>=', $request->fecha_inicio);
            }

            if ($request->filled('fecha_fin')) {
                $query->whereDate('FechaCreacion', '<=', $request->fecha_fin);
            }
        }])->get();

        $reporteEstados = (clone $baseQuery)
            ->select('Estado', DB::raw('count(*) as total'))
            ->groupBy('Estado')
            ->get();

        $reporteDocentes = Usuario::where('IdRol', 3)
            ->withCount(['investigacionesAsignadas as total' => function ($query) use ($request) {
                if ($request->filled('fecha_inicio')) {
                    $query->whereDate('FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $query->whereDate('FechaCreacion', '<=', $request->fecha_fin);
                }
            }])
            ->orderBy('Nombres')
            ->get();

        $documentosQuery = Documento::query();

        if ($request->filled('fecha_inicio')) {
            $documentosQuery->whereDate('Fecha', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $documentosQuery->whereDate('Fecha', '<=', $request->fecha_fin);
        }

        $reporteDocumentos = $documentosQuery
            ->select('tipo_entrega', DB::raw('count(*) as total'))
            ->groupBy('tipo_entrega')
            ->get();

        $investigaciones = (clone $baseQuery)
            ->with('escuela')
            ->orderBy('FechaCreacion', 'desc')
            ->get();

        $maxEscuelas = $reporteEscuelas->max('investigaciones_count') ?: 1;
        $maxEstados = $reporteEstados->max('total') ?: 1;
        $maxDocentes = $reporteDocentes->max('total') ?: 1;
        $maxDocumentos = $reporteDocumentos->max('total') ?: 1;

        $fechaInicio = $request->fecha_inicio ?? 'Todas';
        $fechaFin = $request->fecha_fin ?? 'Todas';

        $pdf = Pdf::loadView('decano.reportes.pdf', compact(
            'total',
            'pendientes',
            'revision',
            'completadas',
            'reporteEscuelas',
            'reporteEstados',
            'reporteDocentes',
            'reporteDocumentos',
            'investigaciones',
            'maxEscuelas',
            'maxEstados',
            'maxDocentes',
            'maxDocumentos',
            'fechaInicio',
            'fechaFin'
        ));

        return $pdf->download('reporte_general_investigaciones.pdf');
    }

    public function exportExcel(Request $request)
    {
        $query = Investigacion::with('escuela');

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('FechaCreacion', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('FechaCreacion', '<=', $request->fecha_fin);
        }

        $investigaciones = $query
            ->orderBy('FechaCreacion', 'desc')
            ->get();

        return Excel::download(new class($investigaciones) implements
            \Maatwebsite\Excel\Concerns\FromCollection,
            \Maatwebsite\Excel\Concerns\WithHeadings
        {
            private $investigaciones;

            public function __construct($investigaciones)
            {
                $this->investigaciones = $investigaciones;
            }

            public function collection()
            {
                return $this->investigaciones->map(function ($i) {
                    return [
                        $i->Titulo,
                        $i->Estado,
                        $i->escuela->Nombre ?? 'Sin escuela',
                        $i->Carrera,
                        $i->Materia,
                        $i->Seccion,
                        $i->Carnet,
                        $i->FechaCreacion,
                    ];
                });
            }

            public function headings(): array
            {
                return [
                    'Título',
                    'Estado',
                    'Escuela',
                    'Carrera',
                    'Materia',
                    'Sección',
                    'Carnet docente',
                    'Fecha creación',
                ];
            }
        }, 'reporte_general_investigaciones.xlsx');
    }
}