<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte General de Investigaciones</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }

        h1 {
            text-align: center;
            color: #5D0A28;
            margin-bottom: 5px;
        }

        h2 {
            color: #5D0A28;
            margin-top: 25px;
            margin-bottom: 10px;
        }

        .subtitulo {
            text-align: center;
            margin-bottom: 20px;
            color: #555;
        }

        .resumen {
            width: 100%;
            margin-bottom: 15px;
        }

        .resumen td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        .numero {
            font-size: 18px;
            font-weight: bold;
            color: #5D0A28;
        }

        .chart-box {
            margin-bottom: 20px;
        }

        .bar-row {
            margin-bottom: 8px;
        }

        .bar-label {
            font-weight: bold;
            margin-bottom: 3px;
        }

        .bar-container {
            width: 100%;
            background: #eee;
            height: 18px;
            border-radius: 4px;
        }

        .bar {
            height: 18px;
            background: #5D0A28;
            color: white;
            font-size: 10px;
            text-align: right;
            padding-right: 5px;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        th {
            background: #5D0A28;
            color: white;
            padding: 6px;
            border: 1px solid #333;
        }

        td {
            padding: 6px;
            border: 1px solid #333;
        }

        .footer {
            margin-top: 25px;
            font-size: 10px;
            text-align: center;
            color: #777;
        }
    </style>
</head>

<body>

<h1>Reporte General de Investigaciones</h1>
<div class="subtitulo">Sistema de Control de Investigaciones Formativas</div>
<p style="text-align:center;">
    <strong>Rango de fechas:</strong> {{ $fechaInicio }} - {{ $fechaFin }}
</p>
<table class="resumen">
    <tr>
        <td>
            <div>Total investigaciones</div>
            <div class="numero">{{ $total }}</div>
        </td>
        <td>
            <div>Pendientes</div>
            <div class="numero">{{ $pendientes }}</div>
        </td>
        <td>
            <div>En revisión</div>
            <div class="numero">{{ $revision }}</div>
        </td>
        <td>
            <div>Completadas</div>
            <div class="numero">{{ $completadas }}</div>
        </td>
    </tr>
</table>

<h2>Gráfico: Investigaciones por escuela</h2>
<div class="chart-box">
    @foreach($reporteEscuelas as $escuela)
        @php
            $porcentaje = ($escuela->investigaciones_count / $maxEscuelas) * 100;
        @endphp

        <div class="bar-row">
            <div class="bar-label">{{ $escuela->Nombre }}</div>
            <div class="bar-container">
                <div class="bar" style="width: {{ $porcentaje }}%;">
                    {{ $escuela->investigaciones_count }}
                </div>
            </div>
        </div>
    @endforeach
</div>

<h2>Gráfico: Investigaciones por estado</h2>
<div class="chart-box">
    @foreach($reporteEstados as $estado)
        @php
            $porcentaje = ($estado->total / $maxEstados) * 100;
        @endphp

        <div class="bar-row">
            <div class="bar-label">{{ $estado->Estado }}</div>
            <div class="bar-container">
                <div class="bar" style="width: {{ $porcentaje }}%;">
                    {{ $estado->total }}
                </div>
            </div>
        </div>
    @endforeach
</div>

<h2>Gráfico: Investigaciones por docente</h2>
<div class="chart-box">
    @foreach($reporteDocentes as $docente)
        @php
            $porcentaje = ($docente->total / $maxDocentes) * 100;
        @endphp

        <div class="bar-row">
            <div class="bar-label">{{ $docente->Nombres }} {{ $docente->Apellidos }}</div>
            <div class="bar-container">
                <div class="bar" style="width: {{ $porcentaje }}%;">
                    {{ $docente->total }}
                </div>
            </div>
        </div>
    @endforeach
</div>

<h2>Gráfico: Documentos por tipo de entrega</h2>
<div class="chart-box">
    @foreach($reporteDocumentos as $documento)
        @php
            $porcentaje = ($documento->total / $maxDocumentos) * 100;
        @endphp

        <div class="bar-row">
            <div class="bar-label">{{ $documento->tipo_entrega ?? 'Sin tipo' }}</div>
            <div class="bar-container">
                <div class="bar" style="width: {{ $porcentaje }}%;">
                    {{ $documento->total }}
                </div>
            </div>
        </div>
    @endforeach
</div>

<h2>Investigaciones por escuela</h2>
<table>
    <thead>
        <tr>
            <th>Escuela</th>
            <th>Total investigaciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reporteEscuelas as $escuela)
        <tr>
            <td>{{ $escuela->Nombre }}</td>
            <td>{{ $escuela->investigaciones_count }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h2>Investigaciones por estado</h2>
<table>
    <thead>
        <tr>
            <th>Estado</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reporteEstados as $estado)
        <tr>
            <td>{{ $estado->Estado }}</td>
            <td>{{ $estado->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h2>Investigaciones por docente</h2>
<table>
    <thead>
        <tr>
            <th>Docente</th>
            <th>Total investigaciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reporteDocentes as $docente)
        <tr>
            <td>{{ $docente->Nombres }} {{ $docente->Apellidos }}</td>
            <td>{{ $docente->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h2>Documentos por tipo de entrega</h2>
<table>
    <thead>
        <tr>
            <th>Tipo de entrega</th>
            <th>Total documentos</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reporteDocumentos as $documento)
        <tr>
            <td>{{ $documento->tipo_entrega ?? 'Sin tipo' }}</td>
            <td>{{ $documento->total }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h2>Detalle general de investigaciones</h2>
<table>
    <thead>
        <tr>
            <th>Título</th>
            <th>Estado</th>
            <th>Escuela</th>
            <th>Carrera</th>
            <th>Materia</th>
            <th>Sección</th>
            <th>Docente</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        @foreach($investigaciones as $i)
        <tr>
            <td>{{ $i->Titulo }}</td>
            <td>{{ $i->Estado }}</td>
            <td>{{ $i->escuela->Nombre ?? 'Sin escuela' }}</td>
            <td>{{ $i->Carrera }}</td>
            <td>{{ $i->Materia }}</td>
            <td>{{ $i->Seccion }}</td>
            <td>{{ $i->Carnet }}</td>
            <td>{{ $i->FechaCreacion }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Reporte generado automáticamente desde el módulo Decano.
</div>
</body>
</html>