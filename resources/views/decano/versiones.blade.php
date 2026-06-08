@extends('layouts.decano')

@section('title', 'Versiones del Documento')

@section('content')

<a href="/decano/investigacion/{{ $documento->IdInvestigacion }}"
    class="btn btn-secondary mb-4 rounded-3">
    ⬅️ Volver
</a>

<div class="card shadow border-0 rounded-4 mb-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <span class="badge bg-dark px-3 py-2 rounded-pill mb-2">
                    {{ $documento->TipoDocumento }}
                </span>

                <h3 class="fw-bold mb-1">
                    {{ $documento->Nombre }}
                </h3>

            </div>

            <div class="text-end">

                <small class="text-muted">
                    {{ $documento->versiones->count() }} versiones
                </small>

            </div>

        </div>

    </div>

</div>

<h4 class="fw-bold mb-4">
    📂 Versiones del documento
</h4>

@if($documento->versiones->isEmpty())

<div class="alert alert-info shadow-sm border-0 rounded-4">
    No hay versiones registradas
</div>

@else

<div class="row">

    @foreach($documento->versiones as $ver)

    <div class="col-md-6 mb-4">

        <div class="card shadow border-0 rounded-4 h-100">

            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <h5 class="fw-bold mb-0">
                        📄 Versión V{{ $ver->NumeroVersion }}
                    </h5>

                    @php
                    $color = match($ver->Estado) {
                    'Pendiente' => 'bg-warning text-dark',
                    'En revisión' => 'bg-info text-dark',
                    'Completado' => 'bg-success',
                    default => 'bg-secondary'
                    };
                    @endphp

                    <span class="badge rounded-pill {{ $color }} px-3 py-2">
                        {{ $ver->Estado }}
                    </span>

                </div>
                <div class="mb-3">

                    <label class="text-muted">
                        Comentario
                    </label>

                    <p class="mb-0">
                        {{ $ver->Comentario ?: 'Sin comentarios' }}
                    </p>
                </div>
                <div class="mb-4">

                    <label class="text-muted">
                        Fecha
                    </label>

                    <p class="mb-0">
                        {{ \Carbon\Carbon::parse($ver->Fecha)->diffForHumans() }}
                    </p>

                </div>
                <a href="/decano/version/{{ $ver->IdVersion }}"
                    class="btn btn-primary w-100 rounded-3">
                    👁 Ver archivo
                </a>

            </div>

        </div>

    </div>

    @endforeach

</div>

@endif

@endsection