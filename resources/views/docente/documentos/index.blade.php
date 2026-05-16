@extends('layouts.app-docente')

@section('page_title')
    Documentos Subidos
@endsection

@section('content')
    <h2 class="mb-4">Documentos Subidos</h2>

    <div class="card mb-4 bg-light border-0">
        <div class="card-body">
            <h5><strong>{{ $investigacion->Titulo }}</strong></h5>
            <p><strong>Carrera:</strong> {{ $investigacion->Carrera }} |
                <strong>Materia:</strong> {{ $investigacion->Materia }} |
                <strong>Sección:</strong> {{ $investigacion->Seccion }}
            </p>
        </div>
    </div>

    <!-- Filtro + Botón Subir -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div class="col-md-5">
            <label class="form-label">Tipo de Entrega</label>
            <select id="filtroTipo" class="form-select" onchange="filtrarDocumentos()">
                <option value="">Todos los documentos</option>
                <option value="avance_1">Avance 1</option>
                <option value="avance_2">Avance 2</option>
                <option value="avance_3">Avance 3</option>
                <option value="final">Documento Final</option>
                <option value="banner">Banner</option>
            </select>
        </div>

        <div>
            <a href="{{ route('docente.investigaciones.subir', $investigacion->IdInvestigacion) }}"
                class="btn btn-success btn-lg">
                + Subir documento
            </a>
        </div>
    </div>

    @if ($documentos->isEmpty())
        <div class="alert alert-info text-center py-5">
            No hay documentos subidos.
        </div>
    @else
        <div class="table-responsive" id="contenedorTabla">
            <table class="table table-hover table-bordered align-middle" id="tablaDocumentos">
                <thead class="table-dark">
                    <tr>
                        <th width="150px">Tipo de Entrega</th>
                        <th>Nombre del Documento</th>
                        <th width="150px">Versión</th>
                        <th width="200px">Fecha de Subida</th>
                        <th width="150px">Estado</th>
                        <th width="300px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documentos as $doc)
                        @foreach ($doc->versions as $version)
                            @if ($version->Estado === 'Pendiente')
                                <!-- Solo Pendientes -->
                                <tr class="documento-row" data-tipo="{{ $doc->tipo_entrega }}">
                                    <td>
                                        <span class="badge bg-info">
                                            @switch($doc->tipo_entrega)
                                                @case('avance_1')
                                                    Avance 1
                                                @break

                                                @case('avance_2')
                                                    Avance 2
                                                @break

                                                @case('avance_3')
                                                    Avance 3
                                                @break

                                                @case('final')
                                                    Documento Final
                                                @break

                                                @case('banner')
                                                    Banner
                                                @break
                                            @endswitch
                                        </span>
                                    </td>
                                    <td><strong>{{ $doc->Nombre }}</strong></td>
                                    <td><span class="badge bg-primary">v{{ $version->NumeroVersion }}</span></td>
                                    <td>{{ date('d/m/Y H:i', strtotime($version->Fecha)) }}</td>
                                    <td>
                                        <span class="badge bg-warning">Pendiente</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-2"
                                            onclick="verArchivo('{{ asset($version->RutaArchivo) }}', '{{ $doc->Nombre }} v{{ $version->NumeroVersion }}')">
                                            👁 Ver
                                        </button>
                                        <a href="{{ route('docente.documentos.descargar', $version->IdVersion) }}"
                                            class="btn btn-sm btn-success me-2">
                                            ⬇ Descargar
                                        </a>
                                        <form
                                            action="{{ route('docente.documentos.eliminar.version', $version->IdVersion) }}"
                                            method="POST" style="display: inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('¿Eliminar esta versión?')">
                                                🗑 Eliminar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        <div id="mensajeVacio" class="alert alert-info text-center py-5 mt-3" style="display:none;">
            No hay documentos para este filtro.
        </div>
    @endif
@endsection


<!-- ==================== MODAL PARA VER DOCUMENTO ==================== -->
<div class="modal fade" id="verModal" tabindex="-1" aria-labelledby="verModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verModalLabel">Vista previa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center" id="modalBody" style="min-height: 650px; background:#f8f9fa;">
                <!-- JS cargará aquí el PDF o imagen -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function verArchivo(url, titulo) {
        document.getElementById('verModalLabel').textContent = titulo;
        const modalBody = document.getElementById('modalBody');
        modalBody.innerHTML = '';

        const ext = url.split('.').pop().toLowerCase();

        if (ext === 'pdf') {
            modalBody.innerHTML = `<iframe src="${url}" width="100%" height="650px"></iframe>`;
        } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
            modalBody.innerHTML = `<img src="${url}" class="img-fluid p-3">`;
        } else {
            modalBody.innerHTML = `
            <div class="text-center py-5">
                <h4>Vista previa no disponible</h4>
                <a href="${url}" class="btn btn-success mt-3" download>⬇ Descargar</a>
            </div>`;
        }

        new bootstrap.Modal(document.getElementById('verModal')).show();
    }

    function filtrarDocumentos() {
        const filtro = document.getElementById('filtroTipo').value;
        const filas = document.querySelectorAll('.documento-row');
        const mensaje = document.getElementById('mensajeVacio');
        const tabla = document.getElementById('contenedorTabla');

        let visibles = 0;

        filas.forEach(fila => {
            if (filtro === '' || fila.getAttribute('data-tipo') === filtro) {
                fila.style.display = '';
                visibles++;
            } else {
                fila.style.display = 'none';
            }
        });

        if (visibles === 0) {
            mensaje.style.display = 'block';
            tabla.style.display = 'none'; // 👈 OCULTA TABLA COMPLETA
        } else {
            mensaje.style.display = 'none';
            tabla.style.display = 'block'; // 👈 MUESTRA TABLA
        }
    }

    document.addEventListener('DOMContentLoaded', filtrarDocumentos);
</script>
