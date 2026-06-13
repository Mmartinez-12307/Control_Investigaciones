<!DOCTYPE html>
<html>

<head>
    <title>Login</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">

    <!-- SweetAlert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('https://www.utec.edu.sv/utecdri/assets/img/img_titulacion.jpg') no-repeat center center;
            background-size: cover;
            height: 100vh;
            margin: 0;
        }

        .overlay {
            background: rgba(0, 0, 0, 0.6);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            width: 380px;
            border-radius: 15px;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .form-control {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
        }

        .form-control::placeholder {
            color: #ddd;
        }

        .form-control:focus {
            background: rgba(255,255,255,0.3);
            color: white;
            box-shadow: none;
        }

        .btn-custom {
            background: #531C2A;
            border: none;
            color: white;
        }

        .btn-custom:hover {
            background: #890940;
        }
    </style>
</head>

<body>

    <div class="overlay">

        <div class="card login-card p-4 shadow-lg">

            <div class="text-center mb-4">
                <h3>Control de Investigaciones Formativas</h3>
                <p class="small">Inicia sesión para continuar</p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-3">
                    <input type="text"
                           name="Carnet"
                           class="form-control"
                           placeholder="Carnet"
                           required>
                </div>

                <div class="mb-3">
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Contraseña"
                           required>
                </div>

                <button class="btn btn-custom w-100">
                    Entrar
                </button>

            </form>

        </div>

    </div>

    @if ($errors->any())
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error de autenticación',
            text: 'Credenciales incorrectas',
            confirmButtonColor: '#531C2A'
        });
    </script>
    @endif

</body>

</html>
