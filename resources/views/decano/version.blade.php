@extends('layouts.decano')

@section('title', 'Visualización de Documento')

@section('content')

<a href="/decano/documento/{{ $version->IdDocumento }}" class="btn btn-secondary mb-4 rounded-3">
    ⬅️ Volver
</a>

<h2 class="fw-bold mb-4">
    📄 Visualización del documento
</h2>
<div class="card shadow border-0 rounded-4">
    <div class="card-body">

        <h5 class="fw-bold mb-4">Información del documento</h5>

        <div class="row">

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded-4 bg-light h-100">
                    <h6 class="text-muted mb-2">📄 Versión</h6>

                    <span class="badge bg-dark px-3 py-2">
                        V{{ $version->NumeroVersion }}
                    </span>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded-4 bg-light h-100">
                    <h6 class="text-muted mb-2">📌 Estado</h6>

                    @php
                    $color = match ($version->Estado) {
                    'Pendiente' => 'bg-warning text-dark',
                    'Pendiente_Nueva_Version' => 'bg-warning text-dark',
                    'En revisión' => 'bg-info text-dark',
                    'Completado' => 'bg-success',
                    'Corregido' => 'bg-secondary',
                    default => 'bg-secondary',
                    };

                    $estadoTexto = match ($version->Estado) {
                    'Pendiente_Nueva_Version' => 'Pendiente nueva versión',
                    default => $version->Estado,
                    };
                    @endphp

                    <span class="badge rounded-pill {{ $color }} px-3 py-2">
                        {{ $estadoTexto }}
                    </span>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded-4 bg-light h-100">
                    <h6 class="text-muted mb-2">📅 Fecha</h6>

                    <p class="mb-0">
                        {{ \Carbon\Carbon::parse($version->Fecha)->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="p-3 border rounded-4 bg-light h-100">
                    <h6 class="text-muted mb-2">👤 Usuario</h6>

                    <p class="mb-0">
                        {{ $version->usuario->Nombres ?? 'No visible' }}
                        {{ $version->usuario->Apellidos ?? '' }}
                    </p>
                </div>
            </div>

        </div>

        <div class="mt-3 p-3 border rounded-4 bg-light">
            <h6 class="text-muted mb-2">💬 Comentario registrado</h6>

            @if($version->Comentario)
            <p class="mb-0">{{ $version->Comentario }}</p>
            @else
            <p class="text-muted mb-0">Sin comentarios registrados</p>
            @endif
        </div>

    </div>
</div>
<div class="card shadow border-0 rounded-4 mb-4">
    <div class="card-body p-2">

        <iframe
            src="{{ asset($version->RutaArchivo) }}"
            width="100%"
            height="760px"
            style="border: none; border-radius: 12px;">
        </iframe>

    </div>
</div>



@endsection