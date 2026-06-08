@extends('layouts.decano')

@section('title', 'Gestionar usuarios')

@section('content')

<h2 class="fw-bold mb-4">👥 Gestionar usuarios</h2>


<div class="card shadow border-0 rounded-4 mb-4">
    <div class="card-body">

        <h5 class="fw-bold mb-3">➕ Agregar usuario</h5>

        <form method="POST" action="/decano/usuarios">
            @csrf

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Nombres</label>
                    <input type="text" name="Nombres" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Apellidos</label>
                    <input type="text" name="Apellidos" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Carnet</label>
                    <input type="text" name="Carnet" class="form-control" required>
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="Clave" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Rol</label>
                    <select name="IdRol" class="form-select" required>
                        <option value="">Seleccione rol</option>

                        @foreach($roles as $rol)
                        <option value="{{ $rol->IdRol }}">
                            {{ $rol->Nombre }}
                        </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Escuela</label>
                    <select name="IdEscuela" class="form-select" required>
                        <option value="">Seleccione escuela</option>

                        @foreach($escuelas as $escuela)
                        <option value="{{ $escuela->IdEscuela }}">
                            {{ $escuela->Nombre }}
                        </option>
                        @endforeach

                    </select>
                </div>

            </div>

            <button class="btn btn-primary rounded-3">
                💾 Guardar usuario
            </button>

        </form>

    </div>
</div>

<div class="card shadow border-0 rounded-4">
    <div class="card-body">

        <h5 class="fw-bold mb-3">📋 Usuarios registrados</h5>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Carnet</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Escuela</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($usuarios as $usuario)
                    <tr>
                        <td>
                            {{ $usuario->Nombres }} {{ $usuario->Apellidos }}
                        </td>

                        <td>{{ $usuario->Carnet }}</td>

                        <td>{{ $usuario->correo ?? 'Sin correo' }}</td>

                        <td>
                            <span class="badge bg-dark rounded-pill">
                                {{ $usuario->rol->Nombre ?? 'Sin rol' }}
                            </span>
                        </td>

                        <td>
                            {{ $usuario->escuela->Nombre ?? 'Sin escuela' }}
                        </td>
                        <td>
                            <button class="btn btn-warning btn-sm rounded-3"
                                data-bs-toggle="modal"
                                data-bs-target="#editarUsuario{{ $usuario->IdUsuario }}">
                                ✏️ Editar
                            </button>
                        </td>
                    </tr>
                    <div class="modal fade" id="editarUsuario{{ $usuario->IdUsuario }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content rounded-4">

                                <div class="modal-header">
                                    <h5 class="modal-title">✏️ Editar usuario</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <form method="POST" action="/decano/usuarios/{{ $usuario->IdUsuario }}">
                                    @csrf
                                    @method('PUT')

                                    <div class="modal-body">

                                        <div class="row">

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Nombres</label>
                                                <input type="text" name="Nombres" class="form-control"
                                                    value="{{ $usuario->Nombres }}" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Apellidos</label>
                                                <input type="text" name="Apellidos" class="form-control"
                                                    value="{{ $usuario->Apellidos }}" required>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Carnet</label>
                                                <input type="text" name="Carnet" class="form-control"
                                                    value="{{ $usuario->Carnet }}" required>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Correo</label>
                                                <input type="email" name="correo" class="form-control"
                                                    value="{{ $usuario->correo }}">
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Nueva contraseña</label>
                                                <input type="password" name="Clave" class="form-control"
                                                    placeholder="Dejar vacío para no cambiar">
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Rol</label>
                                                <select name="IdRol" class="form-select" required>
                                                    @foreach($roles as $rol)
                                                    <option value="{{ $rol->IdRol }}"
                                                        {{ $usuario->IdRol == $rol->IdRol ? 'selected' : '' }}>
                                                        {{ $rol->Nombre }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Escuela</label>
                                                <select name="IdEscuela" class="form-select" required>
                                                    @foreach($escuelas as $escuela)
                                                    <option value="{{ $escuela->IdEscuela }}"
                                                        {{ $usuario->IdEscuela == $escuela->IdEscuela ? 'selected' : '' }}>
                                                        {{ $escuela->Nombre }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>

                                        <small class="text-muted">
                                            Si no deseas cambiar la contraseña, deja el campo vacío.
                                        </small>

                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary rounded-3" data-bs-dismiss="modal">
                                            Cancelar
                                        </button>

                                        <button class="btn btn-primary rounded-3">
                                            Guardar cambios
                                        </button>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
</div>

@endsection
@section('scripts')

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {

        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000
        });

    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {

        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ $errors->first() }}"
        });

    });
</script>
@endif

@endsection