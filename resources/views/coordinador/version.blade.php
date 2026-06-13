@extends('layouts.coordinador')

@section('title', 'Revisión de Documento')

@section('content')

    <a href="/documento/{{ $version->documento->IdDocumento }}" class="btn btn-secondary mb-3">

        ⬅️ Volver

    </a>

    <div class="row">

        <div class="col-md-8">
            <div class="card shadow p-2">
                @php
                    $extension = pathinfo($version->RutaArchivo, PATHINFO_EXTENSION);
                    $esVisualizableEnIframe = in_array(strtolower($extension), ['pdf', 'jpg', 'jpeg', 'png', 'gif']);
                @endphp

                @if ($esVisualizableEnIframe)
                    <iframe src="{{ asset($version->RutaArchivo) }}" width="100%" height="600px"
                        style="border: none;"></iframe>
                @else
                    <div style="height: 600px;"
                        class="d-flex flex-column align-items-center justify-content-center text-center p-4 bg-light rounded">
                        <span style="font-size: 64px;">📄</span>
                        <h5 class="mt-3 text-muted">Vista previa no disponible</h5>
                        <a href="{{ asset($version->RutaArchivo) }}" class="btn btn-primary mt-2" download>
                            ⬇️ Descargar archivo
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow p-3">

                <h5>Información</h5>
                <div class="mb-3 p-3 border rounded bg-light">

                    <h6 class="mb-2">📄 Versión del documento</h6>

                    <span class="badge bg-dark">
                        V{{ $version->NumeroVersion }}
                    </span>

                </div>
                <div class="mb-3 p-3 border rounded bg-light">

                    <h6 class="mb-2">📌 Estado actual</h6>

                    @php
                        $color = match ($version->Estado) {
                            'Pendiente_Nueva_Version' => 'bg-warning',
                            'Completado' => 'bg-success',
                            default => 'bg-secondary',
                        };
                    @endphp

                    <span class="badge {{ $color }}">
                        {{ $version->Estado }}
                    </span>

                </div>

                <div class="mb-3 p-3 border rounded bg-light">

                    <h6 class="mb-2">💬 Comentario actual</h6>

                    @if ($version->Comentario)
                        <p class="mb-0">{{ $version->Comentario }}</p>
                    @else
                        <p class="text-muted mb-0">Sin comentarios aún</p>
                    @endif


                    <hr>

                    <h5>Cambiar Estado</h5>

                    <form method="POST" action="{{ route('coordinador.actualizar.estado', $version->IdVersion) }}">
                        @csrf

                        <select name="Estado" class="form-control mb-2">
                            <option value="Pendiente_Nueva_Version"
                                {{ $version->Estado == 'Pendiente_Nueva_Version' ? 'selected' : '' }}>
                                Pendiente Nueva Versión
                            </option>

                            <option value="Completado" {{ $version->Estado == 'Completado' ? 'selected' : '' }}>
                                Completado
                            </option>
                        </select>

                        <button class="btn btn-warning w-100">
                            Actualizar Estado
                        </button>
                    </form>

                    <hr>

                    <h5>Comentario</h5>

                    <form method="POST" action="/version/{{ $version->IdVersion }}/comentario">
                        @csrf

                        <textarea name="Comentario" class="form-control mb-2" placeholder="Escribe un comentario..."></textarea>

                        <button class="btn btn-primary w-100">Guardar comentario</button>
                    </form>

                </div>
            </div>

        </div>

    @endsection


    @section('scripts')
        @if (session('success'))
            <script>
                document.addEventListener("DOMContentLoaded", function() {

                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: "{{ session('success') }}",
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {

                        @if (session('correo_ok'))
                            Swal.fire({
                                icon: 'success',
                                title: '📧 Correo enviado',
                                text: "{{ session('correo_ok') }}",
                                showConfirmButton: true,
                            });
                        @elseif (session('correo_error'))
                            Swal.fire({
                                icon: 'warning',
                                title: '⚠️ Aviso de correo',
                                text: "{{ session('correo_error') }}",
                                showConfirmButton: true,
                            });
                        @endif

                    });
                });
            </script>
        @endif

        @if ($errors->any())
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Algo salió mal',
                    });
                });
            </script>
        @endif
    @endsection
