@extends('layouts.decano')

@section('title', $escuela->Nombre)

@section('content')

<a href="/decano" class="btn btn-secondary mb-4 rounded-3">
    ⬅️ Volver
</a>

<div class="card shadow border-0 rounded-4 mb-4">
    <div class="card-body">

        <h2 class="fw-bold mb-2">
            @if($escuela->IdEscuela == 1)
            💻 {{ $escuela->Nombre }}
            @else
            🔬 {{ $escuela->Nombre }}
            @endif
        </h2>

        <p class="text-muted mb-0">
            Investigaciones registradas en esta escuela.
        </p>

    </div>
</div>

<div class="card shadow border-0 rounded-4">
    <div class="card-body">

        <h4 class="fw-bold mb-3">
            📚 Investigaciones
        </h4>

        @if($investigaciones->isEmpty())

        <div class="alert alert-info rounded-4">
            No hay investigaciones registradas.
        </div>

        @else

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Docente</th>
                        <th>Materia</th>
                        <th>Estado</th>
                        <th>Creación</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($investigaciones as $inv)

                    <tr>

                        <td>
                            <strong>{{ $inv->Titulo }}</strong>
                        </td>

                        <td>
                            {{ $inv->docente->Nombres ?? '' }}
                            {{ $inv->docente->Apellidos ?? '' }}
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

                            <span class="badge rounded-pill {{ $color }}">
                                {{ $inv->Estado }}
                            </span>

                        </td>

                        <td>
                            {{ \Carbon\Carbon::parse($inv->FechaCreacion)->format('d/m/Y H:i') }}
                        </td>

                        <td>

                            <a href="/decano/investigacion/{{ $inv->IdInvestigacion }}"
                                class="btn btn-primary btn-sm rounded-3">
                                👁 Ver
                            </a>

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        @endif

    </div>
</div>

@endsection