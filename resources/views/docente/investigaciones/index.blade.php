@extends('layouts.app-docente')

@section('page_title')
    Investigaciones
@endsection

@section('content')

<h2 class="mb-2 text-light mb-4">📚 Investigaciones Disponibles</h2>

    <div class="bg-white rounded-4 p-4 shadow-sm text-dark">

        @if ($investigaciones->isEmpty())
            <div class="alert alert-info text-center py-5">
                No hay investigaciones registradas aún.
            </div>
        @else
            <div class="d-flex flex-column gap-4">

                @foreach ($investigaciones as $inv)
                    <div class="card border-0 shadow-sm rounded-4">

                        <div class="card-body">

                            <!-- TÍTULO -->
                            <h5 class="fw-bold mb-2">
                                {{ $inv->Titulo }}
                            </h5>

                            <!-- DESCRIPCIÓN -->
                            <p class="text-muted mb-3">
                                {{ $inv->Descripcion ?? 'Sin descripción disponible.' }}
                            </p>

                            <!-- INFO -->
                            <div class="row mb-3">

                                <div class="col-md-3">
                                    <small class="text-muted">Escuela</small><br>
                                    <strong>{{ $inv->escuela->Nombre ?? '—' }}</strong>
                                </div>

                                <div class="col-md-3">
                                    <small class="text-muted">Carrera</small><br>
                                    <strong>{{ $inv->Carrera }}</strong>
                                </div>

                                <div class="col-md-3">
                                    <small class="text-muted">Materia</small><br>
                                    <strong>{{ $inv->Materia }}</strong>
                                </div>

                                <div class="col-md-1">
                                    <small class="text-muted">Sección</small><br>
                                    <strong>{{ $inv->Seccion }}</strong>
                                </div>

                                <div class="col-md-2">
                                    <small class="text-muted">Fecha</small><br>
                                    <strong>
                                        {{ $inv->FechaCreacion ? date('d/m/Y', strtotime($inv->FechaCreacion)) : '—' }}
                                    </strong>
                                </div>

                            </div>

                            <!-- ACCIONES -->
                            <div class="d-flex justify-content-end gap-2">

                                <a href="{{ route('docente.investigaciones.documentos', $inv->IdInvestigacion) }}"
                                    class="btn btn-primary rounded-pill px-4">
                                    👁 Ver documentos
                                </a>

                                <a href="{{ route('docente.investigaciones.revision', $inv->IdInvestigacion) }}"
                                    class="btn btn-warning rounded-pill px-4">
                                    ✍️ Revisar
                                </a>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
