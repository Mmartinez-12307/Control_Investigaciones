
@extends('layouts.coordinador')

@section('title', 'Revisar Investigaciones')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">

    <h2 class="fw-bold text-dark mb-0">📚 Investigaciones</h2>

    @php
        $color = request()->hasAny(['IdInvestigacion', 'Carnet', 'Estado'])
            ? 'bg-info text-dark'
            : 'bg-dark';
    @endphp

    <span class="badge {{ $color }} rounded-pill px-3 py-2">

        @if(request()->hasAny(['IdInvestigacion', 'Carnet', 'Estado']))
            🔍 {{ $investigaciones->count() }} resultado(s)
        @else
            📊 {{ $investigaciones->count() }} investigación(es)
        @endif

    </span>

</div>

<form method="GET" class="card shadow-sm p-3 mb-4 rounded-4">

    <div class="row">
        <div class="col-md-3">
            <label class="form-label">ID Investigación</label>
            <input type="number" name="IdInvestigacion"
                   value="{{ request('IdInvestigacion') }}"
                   class="form-control form-control-sm">
        </div>
        <div class="col-md-3">
            <label class="form-label">Docente</label>
            <select name="Carnet" class="form-select form-select-sm">
                <option value="">Todos</option>

                @foreach($docentes as $doc)
                    <option value="{{ $doc->Carnet }}"
                        {{ request('Carnet') == $doc->Carnet ? 'selected' : '' }}>
                        {{ $doc->Nombres }} {{ $doc->Apellidos }}
                    </option>
                @endforeach

            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <select name="Estado" class="form-select form-select-sm">
                <option value="">Todos</option>

                <option value="Pendiente" {{ request('Estado') == 'Pendiente' ? 'selected' : '' }}>
                    Pendiente
                </option>

                <option value="En revisión" {{ request('Estado') == 'En revisión' ? 'selected' : '' }}>
                    En revisión
                </option>

                <option value="Completado" {{ request('Estado') == 'Completado' ? 'selected' : '' }}>
                    Completado
                </option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end gap-2">

            <button class="btn btn-primary btn-sm w-100">
                🔍 Filtrar
            </button>

            <a href="/investigaciones" class="btn btn-secondary btn-sm w-100">
                Limpiar
            </a>

        </div>

    </div>

</form>
@if($investigaciones->isEmpty())

    @if(request()->hasAny(['IdInvestigacion', 'Carnet', 'Estado']))
        <div class="alert alert-warning shadow-sm border-0 rounded-4">
            No se encontraron resultados
        </div>
    @else
        <div class="alert alert-info shadow-sm border-0 rounded-4">
            No hay investigaciones registradas
        </div>
    @endif

@else
<div class="card shadow border-0 rounded-4">
    <div class="card-body p-0">

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">

                <thead style="background:#5D0A28; color:white;">
                    <tr>
                        <th class="px-4 py-3">Título</th>
                        <th>Materia</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Docente asignado</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($investigaciones as $inv)
                    <tr>

                        <td class="px-4 fw-semibold">
                            {{ $inv->Titulo }}
                        </td>

                        <td>
                            {{ $inv->Materia }}
                        </td>

                        <td>
                            @php
                                $color = match($inv->Estado) {
                                    'Pendiente' => 'bg-warning text-dark',
                                    'En revisión' => 'bg-info text-dark',
                                    'Completado' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                            @endphp

                            <span class="badge rounded-pill {{ $color }} px-3 py-2">
                                {{ $inv->Estado }}
                            </span>
                        </td>

                        <td>
                            {{ $inv->FechaCreacion }}
                        </td>

                        <td>
                            @if($inv->docente)
                                <span class="fw-semibold">
                                    {{ $inv->docente->Nombres }}
                                    {{ $inv->docente->Apellidos }}
                                </span>
                                <br>
                                <small class="text-muted">
                                    {{ $inv->Carnet }}
                                </small>
                            @else
                                <span class="text-danger fw-semibold">
                                    Sin asignar
                                </span>
                            @endif
                        </td>

                        <td style="width:220px">

                            <a href="/investigacion/{{ $inv->IdInvestigacion }}"
                               class="btn btn-primary btn-sm w-100 mb-2 rounded-3">
                                👁 Ver
                            </a>

                            <form action="/investigacion/{{ $inv->IdInvestigacion }}/estado" method="POST">
                                @csrf

                                <select name="Estado" class="form-select form-select-sm mb-2 rounded-3">
                                    <option value="Pendiente" {{ $inv->Estado == 'Pendiente' ? 'selected' : '' }}>
                                        Pendiente
                                    </option>

                                    <option value="En revisión" {{ $inv->Estado == 'En revisión' ? 'selected' : '' }}>
                                        En revisión
                                    </option>

                                    <option value="Completado" {{ $inv->Estado == 'Completado' ? 'selected' : '' }}>
                                        Completado
                                    </option>
                                </select>

                                <button class="btn btn-success btn-sm w-100 rounded-3">
                                    💾 Guardar
                                </button>

                            </form>

                        </td>

                    </tr>
                    @endforeach

                </tbody>

            </table>
        </div>

    </div>
</div>

@endif

@endsection


@section('scripts')

@if(session('success'))
<script>
document.addEventListener("DOMContentLoaded", function () {
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

@if($errors->any())
<script>
document.addEventListener("DOMContentLoaded", function () {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Algo salió mal'
    });
});
</script>
@endif

@endsection


