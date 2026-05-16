@extends('layouts.app-docente')

@section('page_title')
    Inicio
@endsection

@section('content')

    <h2 class="mb-4">
        Bienvenido, {{ Illuminate\Support\Facades\Auth::user()->Nombres ?? 'Docente' }}
        {{ \Illuminate\Support\Facades\Auth::user()->Apellidos ?? '' }} 👋
    </h2>

    <!-- Tarjetas de resumen -->
    <div class="row g-4 mb-5">

        <!-- Total documentos -->
        <div class="col-md-3">
            <div class="card text-white bg-primary h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5>Total Documentos</h5>
                    <h2 class="mb-0 display-5 fw-bold">{{ $totalDocumentos ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <!-- Pendientes -->
        <div class="col-md-3">
            <div class="card text-white bg-warning h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5>Pendientes</h5>
                    <h2 class="mb-0 display-5 fw-bold">{{ $pendientes ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <!-- Pendiente nueva versión -->
        <div class="col-md-3">
            <div class="card text-white bg-info h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5>Pendiente Nueva Versión</h5>
                    <h2 class="mb-0 display-5 fw-bold">{{ $pendienteNuevaVersion ?? 0 }}</h2>
                </div>
            </div>
        </div>

        <!-- Completados -->
        <div class="col-md-3">
            <div class="card text-white bg-success h-100 shadow-sm">
                <div class="card-body text-center">
                    <h5>Completados</h5>
                    <h2 class="mb-0 display-5 fw-bold">{{ $completados ?? 0 }}</h2>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4">
        <!-- Gráfico -->
        <div class="col-lg-7">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <strong>Selecciona una investigacion</strong>
                    <div style="min-width: 300px;">
                        <select id="filtroInvestigacion" class="form-select form-select-sm">
                            @foreach ($investigaciones as $index => $inv)
                                <option value="{{ $inv->IdInvestigacion }}" {{ $index == 0 ? 'selected' : '' }}>
                                    {{ $inv->Titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body" style="height: 350px;">
                    <canvas id="estadoChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Historial Reciente -->
        <div class="col-lg-5">
            <div class="card h-100 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Historial Reciente</strong>
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('docente.historial') }}" class="btn btn-sm btn-outline-primary rounded-pill">
                            Ver historial completo
                        </a>
                    </div>
                </div>

                <div class="card-body p-0" style="max-height: 420px; overflow-y: auto;">
                    @if ($historial->isEmpty())
                        <div class="text-center py-5 text-muted">
                            Aún no tienes documentos subidos
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($historial as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <strong>{{ $item['Nombre'] }}</strong><br>
                                        <small class="text-muted">
                                            {{ $item['tipo_entrega_text'] }} • v{{ $item['NumeroVersion'] }}
                                        </small>
                                    </div>

                                    <div class="text-end">
                                        <small class="text-muted d-block">
                                            {{ date('d/m/Y', strtotime($item['Fecha'])) }}
                                        </small>

                                        <span
                                            class="badge bg-{{ $item['Estado'] === 'Pendiente'
                                                ? 'warning'
                                                : ($item['Estado'] === 'Revisado'
                                                    ? 'danger'
                                                    : ($item['Estado'] === 'Pendiente_Nueva_Version'
                                                        ? 'info'
                                                        : 'success')) }}">
                                            {{ $item['EstadoTexto'] }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const canvas = document.getElementById('estadoChart');

            if (canvas) {
                const ctx = canvas.getContext('2d');

                window.estadoChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Pendientes', 'Pendiente Nueva Versión', 'Completados'],
                        datasets: [{
                            data: [
                                {{ $pendientes ?? 0 }},
                                {{ $pendienteNuevaVersion ?? 0 }},
                                {{ $completados ?? 0 }}
                            ],
                            backgroundColor: ['#ffc107', '#0dcaf0', '#198754'],
                            borderWidth: 3
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }
                });
            }
            let controller = null;

            document.getElementById('filtroInvestigacion').addEventListener('change', function() {
                const id = this.value;

                // cancelar request anterior
                if (controller) {
                    controller.abort();
                }

                controller = new AbortController();

                fetch(`/docente/dashboard-data/${id}`, {
                        signal: controller.signal
                    })
                    .then(res => res.json())
                    .then(data => {

                        // 🔢 tarjetas
                        document.querySelector('.bg-primary h2').textContent = data.total_documentos;
                        document.querySelector('.bg-warning h2').textContent = data.pendientes;
                        document.querySelector('.bg-info h2').textContent = data.pendiente_nueva_version;
                        document.querySelector('.bg-success h2').textContent = data.completados;
                        // 📊 gráfico
                        estadoChart.data.datasets[0].data = [
                            data.pendientes,
                            data.pendiente_nueva_version,
                            data.completados
                        ];
                        estadoChart.update();
                    })
                    .catch(err => {
                        if (err.name !== 'AbortError') {
                            console.error(err);
                        }
                    });
            });
        });
    </script>
@endsection
