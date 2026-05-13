@extends('layouts.coordinador')

@section('title', 'Inicio')

@section('content')

<form method="GET" class="card p-3 mb-4 shadow-sm rounded-4">

    <div class="row">

        <div class="col-md-4">
            <label class="form-label">Filtrar por docente</label>

            <select name="Carnet" class="form-select">
                <option value="">Todos</option>

                @foreach($docentes as $doc)
                <option value="{{ $doc->Carnet }}"
                    {{ request('Carnet') == $doc->Carnet ? 'selected' : '' }}>
                    {{ $doc->Nombres }} {{ $doc->Apellidos }}
                </option>
                @endforeach

            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="button" id="btnFiltrar" class="btn btn-primary w-100">
                Filtrar
            </button>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <a href="/coordinador" class="btn btn-secondary w-100">Limpiar</a>
        </div>

    </div>

</form>
<div class="row">

    <div class="col-md-3 mb-3">
        <div class="card shadow border-0 rounded-4 text-center p-3">
            <h6 class="text-muted">Total</h6>
            <h2 id="total" class="fw-bold">{{ $total }}</h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card shadow border-0 rounded-4 text-center p-3">
            <h6 class="text-muted">Pendientes</h6>
            <h2 id="pendientes" class="fw-bold text-warning">{{ $pendientes }}</h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card shadow border-0 rounded-4 text-center p-3">
            <h6 class="text-muted">En revisión</h6>
            <h2 id="revision" class="fw-bold text-info">{{ $revision }}</h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card shadow border-0 rounded-4 text-center p-3">
            <h6 class="text-muted">Completadas</h6>
            <h2 id="completadas" class="fw-bold text-success">{{ $completadas }}</h2>
        </div>
    </div>

</div>
<div class="row mt-4">

    <div class="col-md-6">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body">
                <h5 class="mb-3">📊 Estado de Investigaciones</h5>
                <div class="d-flex justify-content-center">
                    <div style="height:300px; width:300px;">
                        <canvas id="graficoEstados"
                            data-pendientes="{{ $pendientes }}"
                            data-revision="{{ $revision }}"
                            data-completadas="{{ $completadas }}">
                        </canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow border-0 rounded-4">
            <div class="card-body">
                <h5 class="mb-3">📚 Investigaciones por Materia</h5>

                <div style="height:300px;">
                    <canvas id="graficoMaterias"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="card shadow border-0 rounded-4 mt-4">
    <div class="card-body">

        <h5 class="mb-3">📄 Últimas investigaciones</h5>

        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Estado</th>
                        <th>Creado</th>
                    </tr>
                </thead>

                <tbody>
                @if($ultimas->isEmpty())
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            No hay investigaciones recientes
                        </td>
                    </tr>
                @else
                    @foreach($ultimas as $inv)
                    <tr onclick="window.location='/investigacion/{{ $inv->IdInvestigacion }}'" style="cursor:pointer;">

                        <td>
                            {{ \Illuminate\Support\Str::limit($inv->Titulo, 40) }}
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
                            {{ \Carbon\Carbon::parse($inv->FechaCreacion)->diffForHumans() }}
                        </td>

                    </tr>
                    @endforeach
                @endif
                </tbody>

            </table>
        </div>

    </div>
</div>

@endsection
@section('scripts')

<script>
    document.addEventListener("DOMContentLoaded", function() {

        const materias = JSON.parse('@json($porMateria)');

        const ctx = document.getElementById('graficoEstados');

        if (ctx && typeof Chart !== 'undefined') {

            const pendientes = parseInt(ctx.dataset.pendientes) || 0;
            const revision = parseInt(ctx.dataset.revision) || 0;
            const completadas = parseInt(ctx.dataset.completadas) || 0;

            window.miGrafico = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Pendiente', 'En revisión', 'Completado'],
                    datasets: [{
                        data: [pendientes, revision, completadas],
                        backgroundColor: ['#facc15', '#38bdf8', '#22c55e']
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 800,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        const ctxMaterias = document.getElementById('graficoMaterias');

        if (ctxMaterias && materias.length > 0) {

            const labels = materias.map(m => m.Materia);
            const data = materias.map(m => m.total);

            window.graficoMaterias = new Chart(ctxMaterias, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Investigaciones',
                        data: data
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 800
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
        const btn = document.getElementById('btnFiltrar');

        if (btn) {
            btn.addEventListener('click', function() {

                const carnet = document.querySelector('[name="Carnet"]').value;

                fetch(`/api/dashboard?Carnet=${carnet}`)
                    .then(res => res.json())
                    .then(data => {
                        if (window.miGrafico) {
                            window.miGrafico.data.datasets[0].data = [
                                data.pendientes,
                                data.revision,
                                data.completadas
                            ];
                            window.miGrafico.update();
                        }
                        document.getElementById('pendientes').textContent = data.pendientes;
                        document.getElementById('revision').textContent = data.revision;
                        document.getElementById('completadas').textContent = data.completadas;

                        document.getElementById('total').textContent =
                            data.pendientes + data.revision + data.completadas;

                    })
                    .catch(error => console.error("Error:", error));

            });
        }

    });
</script>

@endsection