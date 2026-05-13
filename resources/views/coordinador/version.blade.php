@extends('layouts.coordinador')

@section('title', 'Revisión de Documento')

@section('content')

<a href="/documento/{{ $version->documento->IdDocumento }}"
   class="btn btn-secondary mb-3">

    ⬅️ Volver

</a>

<div class="row">

    <div class="col-md-8">
        <div class="card shadow p-2">
            <iframe
                src="{{ asset('storage/' . $version->RutaArchivo) }}"
                width="100%"
                height="600px"
                style="border: none;">
            </iframe>
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
                $color = match($version->Estado) {
                'Pendiente nueva version' => 'bg-warning',
                'Completado' => 'bg-success',
                default => 'bg-secondary'
                };
                @endphp

                <span class="badge {{ $color }}">
                    {{ $version->Estado }}
                </span>

            </div>

            <div class="mb-3 p-3 border rounded bg-light">

                <h6 class="mb-2">💬 Comentario actual</h6>

                @if($version->Comentario)
                <p class="mb-0">{{ $version->Comentario }}</p>
                @else
                <p class="text-muted mb-0">Sin comentarios aún</p>
                @endif


                <hr>

                <h5>Cambiar Estado</h5>

                <form method="POST" action="/version/{{ $version->IdVersion }}/estado">
                    @csrf

                    <select name="Estado" class="form-control mb-2">
                        <option value="Pendiente nueva version" {{ $version->Estado == 'Pendiente nueva version' ? 'selected' : '' }}>Pendiente nueva version</option>
                        <option value="Completado" {{ $version->Estado == 'Completado' ? 'selected' : '' }}>Completado</option>
                    </select>

                    <button class="btn btn-warning w-100">Actualizar Estado</button>
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
    @if(session('success'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
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