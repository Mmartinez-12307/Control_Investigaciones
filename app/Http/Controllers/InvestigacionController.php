<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Investigacion;

class InvestigacionController extends Controller
{

    public function index(Request $request)
    {
        $query = Investigacion::with('docente')
            ->where('IdUsuario', Auth::user()->IdUsuario);

        // 🔍 Filtro por ID
        if ($request->filled('IdInvestigacion')) {
            $query->where('IdInvestigacion', $request->IdInvestigacion);
        }

        // 👨‍🏫 Filtro por docente
        if ($request->filled('Carnet')) {
            $query->where('Carnet', $request->Carnet);
        }

        // 📊 Filtro por estado
        if ($request->filled('Estado')) {
            $query->where('Estado', $request->Estado);
        }

        $investigaciones = $query->get();

        // 👨‍🏫 Lista de docentes para el filtro
        $docentes = \App\Models\Usuario::where('IdRol', 3)
            ->orderBy('Nombres')
            ->get();

        return view('coordinador.investigaciones', compact('investigaciones', 'docentes'));
    }
    public function dashboard(Request $request)
    {
        $query = Investigacion::query();

        if ($request->Carnet) {
            $query->where('Carnet', $request->Carnet);
        }

        $total = $query->count();
        $pendientes = (clone $query)->where('Estado', 'Pendiente')->count();
        $revision = (clone $query)->where('Estado', 'En revisión')->count();
        $completadas = (clone $query)->where('Estado', 'Completado')->count();

        $ultimas = $query->orderBy('FechaCreacion', 'desc')->take(5)->get();

        // 🔥 ESTO TE FALTABA
        $docentes = \App\Models\Usuario::where('IdRol', 3)->get();

        $porMateria = Investigacion::select('Materia', DB::raw('count(*) as total'))
            ->when($request->Carnet, function ($q) use ($request) {
                $q->where('Carnet', $request->Carnet);
            })
            ->groupBy('Materia')
            ->get();

        return view('coordinador.index', compact(
            'total',
            'pendientes',
            'revision',
            'completadas',
            'ultimas',
            'docentes',
            'porMateria'
        ));
    }
    public function dashboardData(Request $request)
    {
        $query = Investigacion::query();

        if ($request->Carnet) {
            $query->where('Carnet', $request->Carnet);
        }

        return response()->json([
            'pendientes' => (clone $query)->where('Estado', 'Pendiente')->count(),
            'revision' => (clone $query)->where('Estado', 'En revisión')->count(),
            'completadas' => (clone $query)->where('Estado', 'Completado')->count(),
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'Titulo' => 'required',
            'Descripcion' => 'required',
            'Carrera' => 'nullable',
            'Materia' => 'required',
            'Seccion' => 'nullable',
            'IdEscuela' => 'required'
        ]);

        Investigacion::create([
            'Titulo' => $request->Titulo,
            'Descripcion' => $request->Descripcion,
            'FechaCreacion' => now(),
            'Estado' => 'Pendiente',
            'IdUsuario' => Auth::user()->IdUsuario,
            'IdEscuela' => $request->IdEscuela,
            'Carrera' => $request->Carrera,
            'Materia' => $request->Materia,
            'Seccion' => $request->Seccion,
            'Carnet' => $request->Carnet,
        ]);

        return back()->with('success', 'Investigación creada correctamente');
    }

    public function show($id)
    {
        $investigacion = Investigacion::with('documentos.versiones', 'docente')->findOrFail($id);

        return view('coordinador.show', compact('investigacion'));
    }

    public function updateEstado(Request $request, $id)
    {
        $request->validate([
            'Estado' => 'required|in:Pendiente,En revisión,Completado'
        ]);

        $investigacion = Investigacion::findOrFail($id);

        $investigacion->Estado = $request->Estado;
        $investigacion->save();

        return back()->with('success', 'Estado actualizado correctamente');
    }
}
