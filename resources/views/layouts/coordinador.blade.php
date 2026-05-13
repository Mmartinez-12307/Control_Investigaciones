<!DOCTYPE html>
<html>
<head>
    <title>Coordinador</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
        }

        .sidebar-header {
            background: #3B0820; 
            padding: 20px;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .sidebar {
            width: 250px;
            height: 100vh; 
            background: #5D0A28;
            color: white;
            position: fixed;
        }

        .sidebar a {
            display: block;
            padding: 15px;
            color: white;
            text-decoration: none;
        }

        .sidebar a:hover {
            background: #334155;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }
       .header {
            width: 100%;
            height: 78px;
            background: #5D0A28;
            color:white;
            border-bottom: 1px solid #ddd;

            position: sticky;
            top: 0;

            z-index: 1000;
        }
    </style>
</head>

<body>

<div class="sidebar">
    <div class="sidebar-header text-center">
        <h4>Coordinador</h4>
    </div>

    <a href="/coordinador">🏠 Inicio</a>
    <a href="/investigacion/create">➕ Nueva Investigación</a>
    <a href="/investigaciones">📄 Revisar Mis Investigaciones</a>

    <div style="position: absolute; bottom: 20px; width: 100%;">

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="
            width: 100%;
            background: none;
            border: none;
            color: white;
            padding: 15px;
            text-align: left;
        ">
            🚪 Cerrar sesión
        </button>
    </form>

</div>
</div>

<div class="content p-0">

    <div class="header d-flex justify-content-between align-items-center px-4">
        <h4 class="mb-0">@yield('title', 'Panel')</h4>

        <div>
            👤 {{ auth()->user()->Nombres }} {{ auth()->user()->Apellidos }}
        </div>
    </div>

    <div class="p-4">
        @yield('content')
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@yield('scripts')
</body>
</html> 