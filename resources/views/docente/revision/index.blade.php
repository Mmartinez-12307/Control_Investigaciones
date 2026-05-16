@extends('layouts.app-docente')

@section('page_title')
    Revisión de Documentos
@endsection

@section('content')
    <h2 class="mb-4">Revisión - {{ $investigacion->Titulo }}</h2>

    <div class="card mb-4 bg-light border-0">
        <div class="card-body">
            <p><strong>Carrera:</strong> {{ $investigacion->Carrera }} |
                <strong>Materia:</strong> {{ $investigacion->Materia }} |
                <strong>Sección:</strong> {{ $investigacion->Seccion }}
            </p>
        </div>
    </div>

    <!-- Cuadro 1: Documentos Observados -->
    @php
        $hayObservados = false;
    @endphp
    @foreach ($documentos as $doc)
        @foreach ($doc->versions as $version)
            @if ($version->Estado === 'Pendiente_Nueva_Version')
                @php $hayObservados = true; @endphp
            @endif
        @endforeach
    @endforeach
    @if (!$hayObservados)
        <div class="alert alert-info text-center py-5">
            No hay documentos pendientes de corrección.
        </div>
    @else
        <h5 class="mb-3 text-danger">⏳ Pendiente de Nueva Versión</h5>
        <div class="table-responsive mb-5">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="150px">Tipo de Entrega</th>
                        <th width="300">Nombre del Documento</th>
                        <th width="100px">Versión</th>
                        <th width="200px">Fecha</th>
                        <th width="100px">Estado</th>
                        <th width="300px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documentos as $doc)
                        @foreach ($doc->versions as $version)
                            @if ($version->Estado === 'Pendiente_Nueva_Version')
                                <tr>
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
                                        <span class="badge bg-warning">Pendiente Nueva Version</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-2"
                                            onclick="verArchivo('{{ asset($version->RutaArchivo) }}', '{{ $doc->Nombre }} v{{ $version->NumeroVersion }}')">
                                            👁 Ver
                                        </button>
                                        <button class="btn btn-sm btn-warning me-2"
                                            onclick="subirNuevaVersion({{ $doc->IdDocumento }})">
                                            📤 Nueva Versión
                                        </button>
                                        <button class="btn btn-sm btn-info"
                                            onclick="mostrarComentario('{{ addslashes($version->Comentario ?? '') }}', {{ $version->IdVersion }}, '{{ $doc->Nombre }} v{{ $version->NumeroVersion }}', {{ $doc->IdDocumento }})">
                                            💬 Comentarios 
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Cuadro 2: Pendiente de Nueva Versión -->
    @php
        $hayPendientes = false;
    @endphp
    @foreach ($documentos as $doc)
        @foreach ($doc->versions as $version)
            @if ($version->Estado === 'Completado')
                @php $hayPendientes = true; @endphp
            @endif
        @endforeach
    @endforeach
    @if (!$hayPendientes)
        <div class="alert alert-info text-center py-5">
            Aun no hay documentos aprobados.
        </div>
    @else
        <h5 class="mb-3 text-warning">📋 Documentos Aprobados</h5>
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="150px">Tipo de Entrega</th>
                        <th width="300">Nombre del Documento</th>
                        <th width="100px">Versión</th>
                        <th width="200px">Fecha</th>
                        <th width="100px">Estado</th>
                        <th width="300px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($documentos as $doc)
                        @foreach ($doc->versions as $version)
                            @if ($version->Estado === 'Completado')
                                <tr>
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

                                                @case('extra')
                                                    Extra
                                                @break
                                            @endswitch
                                        </span>
                                    </td>
                                    <td><strong>{{ $doc->Nombre }}</strong></td>
                                    <td><span class="badge bg-primary">v{{ $version->NumeroVersion }}</span></td>
                                    <td>{{ date('d/m/Y H:i', strtotime($version->Fecha)) }}</td>
                                    <td>
                                        <span class="badge bg-info">Completado</span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary me-2"
                                            onclick="verArchivo('{{ asset($version->RutaArchivo) }}', '{{ $doc->Nombre }} v{{ $version->NumeroVersion }}')">
                                            👁 Ver
                                        </button>
                                        <button class="btn btn-sm btn-info me-2"
                                            onclick="mostrarComentario('{{ addslashes($version->Comentario ?? '') }}', {{ $version->IdVersion }}, '{{ $doc->Nombre }} v{{ $version->NumeroVersion }}', {{ $doc->IdDocumento }})">
                                            💬 Comentarios
                                        </button>
                                        <a href="{{ route('docente.documentos.descargar', $version->IdVersion) }}"
                                            class="btn btn-sm btn-success">
                                            ⬇ Descargar
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
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
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL COMENTARIOS ==================== -->
<div class="modal fade" id="modalComentario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalTitulo">Comentarios</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="textoComentario" class="bg-light p-4 rounded border"
                    style="min-height: 200px; white-space: pre-wrap;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
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

    function subirNuevaVersion(documentoId) {
        window.location.href = `/docente/documentos/${documentoId}/nueva-version`;
    }

    let tituloActual = '';
    let comentarioActual = '';
    let versionActualId = null;
    let documentoActualId = null;

    function mostrarComentario(comentario, versionId, titulo, documentoId) {
        tituloActual = titulo;
        comentarioActual = comentario || 'Sin comentarios aún.';
        versionActualId = versionId;
        documentoActualId = documentoId;

        document.getElementById('modalTitulo').textContent = `Comentarios - ${titulo}`;
        document.getElementById('textoComentario').innerHTML = comentarioActual.replace(/\n/g, '<br>');

        new bootstrap.Modal(document.getElementById('modalComentario')).show();
    }
</script>
