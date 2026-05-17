@extends('layouts.coordinador')

@section('title', 'Detalle de Investigación')

@section('content')
<a href="/investigaciones" class="btn btn-secondary mb-3">
    ⬅️ Volver
</a>

<h2 class="fw-bold mb-4">
    📚 {{ $investigacion->Titulo }}
</h2>
<div class="card shadow border-0 rounded-4 mb-4">
    <div class="card-body">

        <div class="row">

            <div class="col-md-6 mb-3">
                <label class="text-muted">Descripción</label>
                <h6>{{ $investigacion->Descripcion }}</h6>
            </div>

            <div class="col-md-3 mb-3">
                <label class="text-muted">Materia</label>
                <h6>{{ $investigacion->Materia }}</h6>
            </div>

            <div class="col-md-3 mb-3">
                <label class="text-muted">Estado</label>

                @php
                $color = match($investigacion->Estado) {
                'Pendiente' => 'bg-warning text-dark',
                'En revisión' => 'bg-info text-dark',
                'Completado' => 'bg-success',
                default => 'bg-secondary'
                };
                @endphp

                <span class="badge rounded-pill {{ $color }} px-3 py-2">
                    {{ $investigacion->Estado }}
                </span>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Carrera</label>
                <h6>{{ $investigacion->Carrera }}</h6>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Sección</label>
                <h6>{{ $investigacion->Seccion }}</h6>
            </div>

            <div class="col-md-4">
                <label class="text-muted">Docente</label>
                <h6>
                    {{ $investigacion->docente->Nombres ?? 'Sin asignar' }}
                    {{ $investigacion->docente->Apellidos ?? '' }}
                </h6>
            </div>

        </div>

    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-3">

    <h4 class="fw-bold mb-0">
        📄 Documentos
    </h4>

</div>

@if($investigacion->documentos->isEmpty())

<div class="alert alert-info shadow-sm border-0 rounded-4">
    No hay documentos registrados
</div>

@else

<div class="row">

    @foreach($investigacion->documentos as $doc)

    <div class="col-md-6 mb-4">

        <div class="card shadow border-0 rounded-4 h-100">

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">

                    <span class="badge bg-dark px-3 py-2 rounded-pill">
                        {{ $doc->tipo_entrega }}
                    </span>

                    <small class="text-muted">
                        {{ $doc->versiones->count() }} versiones
                    </small>

                </div>
                <h5 class="fw-bold">
                    {{ $doc->Nombre }}
                </h5>
                <p class="text-muted mb-4">
                    Última actualización:
                    {{ \Carbon\Carbon::parse($doc->Fecha)->diffForHumans() }}
                </p>
                <a href="/documento/{{ $doc->IdDocumento }}"
                    class="btn btn-primary w-100 rounded-3">

                    📂 Ver versiones

                </a>

            </div>

        </div>

    </div>

    @endforeach

</div>

@endif

@endsection