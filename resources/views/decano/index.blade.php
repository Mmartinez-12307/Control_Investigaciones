@extends('layouts.decano')

@section('title', 'Inicio')

@section('content')

<h2 class="fw-bold mb-2">🏛 Investigaciones Disponibles</h2>
<p class="text-muted mb-4">Seleccione una escuela para revisar sus investigaciones.</p>

<div class="row mb-4">

    <div class="col-md-3 mb-3">
        <div class="card shadow border-0 rounded-4 text-center p-3">
            <h6 class="text-muted">Total investigaciones</h6>
            <h2 class="fw-bold">{{ $total }}</h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card shadow border-0 rounded-4 text-center p-3">
            <h6 class="text-muted">Pendientes</h6>
            <h2 class="fw-bold text-warning">{{ $pendientes }}</h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card shadow border-0 rounded-4 text-center p-3">
            <h6 class="text-muted">En revisión</h6>
            <h2 class="fw-bold text-info">{{ $revision }}</h2>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card shadow border-0 rounded-4 text-center p-3">
            <h6 class="text-muted">Completadas</h6>
            <h2 class="fw-bold text-success">{{ $completadas }}</h2>
        </div>
    </div>

</div>

<div class="row">
    @foreach($escuelas as $escuela)
    <div class="col-md-6 mb-4">
        <div class="card school-card shadow border-0 rounded-4 h-100">
            <div class="card-body p-4">

                <h3 class="fw-bold mb-3">
                    @if($escuela->IdEscuela == 1)
                        💻 {{ $escuela->Nombre }}
                    @else
                        🔬 {{ $escuela->Nombre }}
                    @endif
                </h3>

                <div class="mb-3">
                    <span class="badge bg-dark rounded-pill px-3 py-2">
                        {{ $escuela->investigaciones_count }} investigaciones
                    </span>
                </div>

                <p class="text-muted">
                    Consulte las investigaciones registradas para esta escuela.
                </p>

                <a href="/decano/escuela/{{ $escuela->IdEscuela }}" class="btn btn-primary w-100 rounded-3">
                    Ver investigaciones
                </a>

            </div>
        </div>
    </div>
    @endforeach
</div>

<hr class="my-4">

<h4 class="fw-bold mb-3">📊 Reportes generales</h4>
<form method="GET" action="{{ route('decano.index') }}" class="row mb-4">

    <div class="col-md-3">
        <label class="form-label">Fecha inicial</label>
        <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
    </div>

    <div class="col-md-3">
        <label class="form-label">Fecha final</label>
        <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
    </div>

    <div class="col-md-6 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary">
            Filtrar 
        </button>

        <a href="{{ route('decano.index') }}" class="btn btn-secondary">
            Limpiar
        </a>

        <a href="{{ route('decano.reportes.pdf', request()->query()) }}" class="btn btn-primary">
            Exportar PDF
        </a>

        <a href="{{ route('decano.reportes.excel', request()->query()) }}" class="btn btn-primary">
            Exportar Excel
        </a>
    </div>

</form>
<div class="d-flex gap-2 mb-4">
    <a href="{{ route('decano.reportes.pdf') }}" class="btn btn-primary">
        Exportar reporte general PDF
    </a>

    <a href="{{ route('decano.reportes.excel') }}" class="btn btn-primary">
        Exportar reporte general Excel
    </a>
</div>
<form method="GET" action="{{ route('decano.index') }}" id="formReportes">

    <div class="row">

        <div class="col-md-6 mb-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body">
                    <h5 class="mb-3">Investigaciones por escuela</h5>
                    <div style="height:300px;">
                        <canvas id="graficoEscuelas"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body">
                    <h5 class="mb-3">Investigaciones por estado</h5>
                    <div style="height:300px;">
                        <canvas id="graficoEstados"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body">
                    <h5 class="mb-3">Investigaciones por docente</h5>
                    <div style="height:300px;">
                        <canvas id="graficoDocentes"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body">
                    <h5 class="mb-3">Documentos por tipo</h5>
                    <div style="height:300px;">
                        <canvas id="graficoDocumentos"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

</form>

@endsection

@section('data')
<script id="reportes-data" type="application/json">
{!! json_encode([
    'escuelas' => $reporteEscuelas,
    'estados' => $reporteEstados,
    'docentes' => $reporteDocentes,
    'documentos' => $reporteDocumentos,
]) !!}
</script>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {

    const reportes = JSON.parse(document.getElementById('reportes-data').textContent);

    const reporteEscuelas = reportes.escuelas || [];
    const reporteEstados = reportes.estados || [];
    const reporteDocentes = reportes.docentes || [];
    const reporteDocumentos = reportes.documentos || [];

    new Chart(document.getElementById('graficoEscuelas'), {
        type: 'bar',
        data: {
            labels: reporteEscuelas.map(e => e.Nombre),
            datasets: [{
                label: 'Investigaciones',
                data: reporteEscuelas.map(e => e.investigaciones_count)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    new Chart(document.getElementById('graficoEstados'), {
        type: 'doughnut',
        data: {
            labels: reporteEstados.map(e => e.Estado),
            datasets: [{
                data: reporteEstados.map(e => e.total),
                backgroundColor: ['#facc15', '#38bdf8', '#22c55e', '#94a3b8']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    new Chart(document.getElementById('graficoDocentes'), {
        type: 'bar',
        data: {
            labels: reporteDocentes.map(d => {
                let nombre = d.Nombres + ' ' + d.Apellidos;
                return nombre.length > 16 ? nombre.substring(0, 16) + '...' : nombre;
            }),
            datasets: [{
                label: 'Investigaciones',
                data: reporteDocentes.map(d => d.total)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 0,
                        minRotation: 0
                    }
                },
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    new Chart(document.getElementById('graficoDocumentos'), {
        type: 'bar',
        data: {
            labels: reporteDocumentos.map(d => d.tipo_entrega || 'Sin tipo'),
            datasets: [{
                label: 'Documentos',
                data: reporteDocumentos.map(d => d.total)
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

});
</script>
@endsection