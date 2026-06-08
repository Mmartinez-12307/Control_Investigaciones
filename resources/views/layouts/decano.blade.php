<!DOCTYPE html>
<html>

<head>
    <title>Decano</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            background: #f5f6fa;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #3B0820;
            color: white;
            position: fixed;
        }

        .sidebar-header {
            background: #260514;
            padding: 20px;
            text-align: center;
            font-weight: bold;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #5D0A28;
        }

        .content {
            margin-left: 250px;
        }

        .header {
            height: 78px;
            background: #5D0A28;
            color: white;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .school-card:hover {
            transform: translateY(-4px);
            transition: 0.2s ease-in-out;
        }
    </style>
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <h4>Decano</h4>
        </div>

        <a href="/decano">🏠 Inicio</a>
        <a href="/decano/usuarios">👥 Gestionar usuarios</a>

        <form method="POST" action="{{ route('logout') }}" style="position:absolute; bottom:20px; width:100%;">
            @csrf
            <button type="submit" style="width:100%; background:none; border:none; color:white; padding:15px; text-align:left;">
                🚪 Cerrar sesión
            </button>
        </form>
    </div>

    <div class="content">

        <div class="header d-flex justify-content-between align-items-center px-4">
            <h4 class="mb-0">@yield('title', 'Panel Decano')</h4>

            <div>
                👤 {{ auth()->user()->Nombres }} {{ auth()->user()->Apellidos }}
            </div>
        </div>

        <div class="p-4">
            @yield('content')
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @yield('data')
    @yield('scripts')
</body>

</html>