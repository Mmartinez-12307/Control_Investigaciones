@extends('layouts.app-docente')

@section('page_title')
    Subir Nueva Versión
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Nueva Versión</h2>
        <a href="{{ route('docente.investigaciones.revision', $documento->IdInvestigacion) }}" class="btn btn-secondary">
            ← Volver a Revisión
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header text-white" style="background-color: #9f1239;">
            <strong>Documento:</strong> {{ $documento->Nombre }} 
            <span class="badge bg-light text-dark ms-2">v{{ $siguienteVersion }}</span>
        </div>
        <div class="card-body">
            <p><strong>Investigación:</strong> {{ $documento->investigacion->Titulo }}</p>
            <p><strong>Tipo de Entrega:</strong> 
                @switch($documento->tipo_entrega)
                    @case('avance_1') Avance 1 @break
                    @case('avance_2') Avance 2 @break
                    @case('avance_3') Avance 3 @break
                    @case('final') Documento Final @break
                    @case('banner') Banner @break
                @endswitch
            </p>
        </div>
    </div>

    <div class="mt-4">
        <form action="{{ route('docente.nueva.version.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="IdDocumento" value="{{ $documento->IdDocumento }}">

            <div class="mb-3">
                <label class="form-label">Seleccionar archivo (Versión {{ $siguienteVersion }})</label>
                <input type="file" name="archivos[]" class="form-control" required accept=".pdf,.doc,.docx,.jpg,.png">
                <small class="text-muted">Máximo 10MB</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Comentario (opcional)</label>
                <textarea name="Comentario" class="form-control" rows="4" 
                    placeholder="Ej: Correcciones realizadas según observaciones..."></textarea>
            </div>

            <button type="submit" class="btn btn-success btn-lg">
                📤 Subir Versión {{ $siguienteVersion }}
            </button>
        </form>
    </div>

@endsection