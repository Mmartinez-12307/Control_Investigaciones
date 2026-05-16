<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Docente - Control de Investigaciones</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
            background: #f8f9fa;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #202020;
            color: white;
            position: fixed;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar .brand {
            padding: 1.8rem 1.25rem;
            font-size: 1.45rem;
            font-weight: 700;
            background: #090909;
            text-align: center;
            border-bottom: 1px solid #5D0A28;
        }

        .sidebar a {
            display: block;
            padding: 0.95rem 1.4rem;
            color: #e2e8f0;
            text-decoration: none;
            font-size: 1.05rem;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #5D0A28;
            color: white;
        }

        /* Contenido principal */
        .content-area {
            margin-left: 260px;
            min-height: 100vh;
        }

        /* Header superior estilo UTEC */
        .main-header {
            background: #8B0F38;
            color: white;
            padding: 1.2rem 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Cuadro principal rojo con borde blanco */
        .main-card {
            background: #5D0A28;
            color: white;
            border: 4px solid white;
            border-radius: 10px;
            padding: 30px;
            margin: 25px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            min-height: 75vh;
        }

        .main-card h2 {
            border-bottom: 2px solid rgba(255, 255, 255, 0.4);
            padding-bottom: 12px;
            margin-bottom: 25px;
        }
    </style>
</head>

<body>

    <!-- ====================== SIDEBAR ====================== -->
    <div class="sidebar">
        <div class="brand">DOCENTE</div>

        <a href="{{ url('/docente') }}"
            class="{{ request()->is('docente') && !request()->is('docente/*') ? 'active' : '' }}">
            🏠 Inicio
        </a>

        <a href="{{ route('docente.investigaciones.index') }}"
            class="{{ request()->is('docente/investigaciones*') ? 'active' : '' }}">
            📚 Investigaciones
        </a>

        <div style="position: absolute; bottom: 40px; width: 100%; padding: 0 20px;">

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit"
                    style="
            width: 100%;
            background: none;
            border: none;
            color: #ff6b6b;
            font-weight: bold;
            text-align: left;
            padding: 10px 0;
        ">
                    🚪 Cerrar sesión
                </button>
            </form>
        </div>

    </div>

    <!-- ====================== CONTENIDO ====================== -->
    <div class="content-area">

        <!-- Header Superior -->
        <div class="main-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0 fw-bold">@yield('page_title', 'Control de Investigaciones')</h4>
            </div>
            <div class="text-end">
                <strong>
                    {{ Illuminate\Support\Facades\Auth::user()->Nombres ?? 'Docente' }}
                    {{ Illuminate\Support\Facades\Auth::user()->Apellidos ?? '' }}
                </strong><br>
                <small>{{ Illuminate\Support\Facades\Auth::user()->correo ?? 'Sin correo registrado' }}</small>
            </div>
        </div>

        <!-- Cuadro Principal Rojo -->
        <div class="main-card">
            @yield('content')
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>

</html>
