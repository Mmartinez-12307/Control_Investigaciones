@extends('layouts.app-docente')

@section('page_title')
    Historial Completo
@endsection

@section('content')
    <h2 class="mb-4 text-light">📜 Historial Completo</h2>

    <form method="GET" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <select name="tipo_entrega" class="form-select" onchange="this.form.submit()">
                    <option value="todos">Todos los documentos</option>
                    <option value="avance_1" {{ $filtro == 'avance_1' ? 'selected' : '' }}>Avance 1</option>
                    <option value="avance_2" {{ $filtro == 'avance_2' ? 'selected' : '' }}>Avance 2</option>
                    <option value="avance_3" {{ $filtro == 'avance_3' ? 'selected' : '' }}>Avance 3</option>
                    <option value="final" {{ $filtro == 'final' ? 'selected' : '' }}>Documento Final</option>
                    <option value="banner" {{ $filtro == 'banner' ? 'selected' : '' }}>Banner</option>
                    <option value="extra" {{ $filtro == 'extra' ? 'selected' : '' }}>Extra</option>
                </select>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-4 p-4 shadow-sm text-dark">

        @if ($historial->isEmpty())
            <div class="alert alert-info text-center py-5">
                No hay registros en el historial.
            </div>
        @else
            <div class="d-flex flex-column gap-3">

                @foreach ($historial as $item)
                    @php
                        $version = $item->versions->first();
                    @endphp

                    @if ($version)
                        <div class="border rounded-4 p-3 shadow-sm">

                            <div class="d-flex justify-content-between">

                                <!-- INFO -->
                                <div>
                                    <strong class="fs-5">{{ $item->Nombre }}</strong>

                                    <div class="text-muted small mt-1">
                                        {{ $item->tipo_entrega }} • v{{ $version->NumeroVersion }}
                                    </div>

                                    <div class="text-muted small">
                                        📅 {{ date('d/m/Y H:i', strtotime($version->Fecha)) }}
                                    </div>
                                </div>

                                <!-- ESTADO -->
                                <div class="text-end">
                                    <span
                                        class="badge bg-{{ $version->Estado === 'Pendiente'
                                            ? 'warning'
                                            : ($version->Estado === 'Pendiente_Nueva_Version'
                                                ? 'info'
                                                : ($version->Estado === 'Corregido'
                                                    ? 'secondary'
                                                    : 'success')) }}">
                                        {{ $version->Estado }}
                                    </span>
                                </div>

                            </div>

                            <!-- ACCIONES -->
                            <div class="mt-3 d-flex gap-2">

                                <button class="btn btn-primary btn-sm rounded-pill"
                                    onclick="verArchivo('{{ asset($version->RutaArchivo) }}', '{{ $item->Nombre }} v{{ $version->NumeroVersion }}')">
                                    👁 Ver
                                </button>

                                <a href="{{ route('docente.documentos.descargar', $version->IdVersion) }}"
                                    class="btn btn-success btn-sm rounded-pill">
                                    ⬇ Descargar
                                </a>

                                @if ($item->versions->count() > 1)
                                    <button class="btn btn-dark btn-sm rounded-pill"
                                        onclick="toggleSeguimiento({{ $item->IdDocumento }})">
                                        🕒 Ver seguimiento
                                    </button>
                                @endif
                            </div>
                            @if ($item->versions->count() > 1)
                                <div id="seguimiento-{{ $item->IdDocumento }}" class="mt-3 d-none border-top pt-3">

                                    <h6 class="mb-3 text-muted">Versiones anteriores</h6>

                                    @foreach ($item->versions->skip(1) as $oldVersion)
                                        <div class="border rounded p-3 mb-2 bg-light">

                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <strong>v{{ $oldVersion->NumeroVersion }}</strong>
                                                    <div class="small text-muted">
                                                        {{ date('d/m/Y H:i', strtotime($oldVersion->Fecha)) }}
                                                    </div>

                                                    <div class="small">
                                                        Comentario:
                                                        {{ $oldVersion->Comentario ?? 'Sin comentarios' }}
                                                    </div>
                                                </div>

                                                <div class="text-end">
                                                    <span class="badge bg-secondary">
                                                        {{ $oldVersion->Estado }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="mt-2 d-flex gap-2">
                                                <button class="btn btn-primary btn-sm"
                                                    onclick="verArchivo('{{ asset($oldVersion->RutaArchivo) }}',
                                                        '{{ $item->Nombre }} v{{ $oldVersion->NumeroVersion }}')">
                                                    👁 Ver
                                                </button>

                                                <a href="{{ route('docente.documentos.descargar', $oldVersion->IdVersion) }}"
                                                    class="btn btn-success btn-sm">
                                                    ⬇ Descargar
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
@endsection


<!-- ==================== MODAL PARA VER DOCUMENTO ==================== -->
<div class="modal fade" id="verModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verModalLabel">Vista previa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 text-center" id="modalBody" style="min-height: 650px; background:#f8f9fa;">
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

    function toggleSeguimiento(documentoId) {
        const contenedor = document.getElementById('seguimiento-' + documentoId);

        contenedor.classList.toggle('d-none');
    }
</script>
