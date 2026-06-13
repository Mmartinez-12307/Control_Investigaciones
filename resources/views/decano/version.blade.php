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

        @php
            $extension = strtolower(pathinfo($version->RutaArchivo, PATHINFO_EXTENSION));
            $esBanner = strtolower($version->documento->tipo_entrega ?? '') === 'banner';
            $esImagen = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            $esPdf = $extension === 'pdf';
        @endphp

        @if ($esBanner && $esImagen)
            {{-- Banner: imagen centrada + botón de descarga --}}
            <div class="d-flex flex-column align-items-center justify-content-center p-4">
                <img src="{{ asset($version->RutaArchivo) }}"
                    class="img-fluid rounded-4 shadow mb-4"
                    style="max-height: 600px; object-fit: contain;"
                    alt="Banner del documento">

                <a href="{{ asset($version->RutaArchivo) }}"
                    class="btn btn-primary"
                    download>
                    ⬇️ Descargar banner
                </a>
            </div>

        @elseif ($esPdf)
            {{-- PDF: vista previa normal --}}
            <iframe
                src="{{ asset($version->RutaArchivo) }}"
                width="100%"
                height="760px"
                style="border: none; border-radius: 12px;">
            </iframe>

        @else
            {{-- Otros archivos (Word, etc): sin vista previa --}}
            <div style="height: 300px;" class="d-flex flex-column align-items-center justify-content-center text-center p-4 bg-light rounded-4">
                <span style="font-size: 64px;">📄</span>
                <h5 class="mt-3 text-muted">Vista previa no disponible</h5>
                <a href="{{ asset($version->RutaArchivo) }}" class="btn btn-primary mt-2" download>
                    ⬇️ Descargar archivo
                </a>
            </div>
        @endif

    </div>
</div>



@endsection