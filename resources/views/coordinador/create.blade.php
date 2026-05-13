@extends('layouts.coordinador')

@section('title', 'Nueva Investigación')

@section('content')

<div class="container" style="max-width: 900px;">

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header text-white rounded-top-4" style="background:#5D0A28;">
            <h4 class="mb-0">📄 Nueva Investigación</h4>
        </div>

        <div class="card-body p-4">

            <form id="formInvestigacion" method="POST" action="/investigacion">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Título</label>
                    <input type="text" name="Titulo" class="form-control rounded-3" placeholder="Ej: Sistema de gestión..." required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Descripción</label>
                    <textarea name="Descripcion" class="form-control rounded-3" rows="3" placeholder="Describe brevemente la investigación..." required></textarea>
                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Carrera</label>
                        <input type="text" name="Carrera" class="form-control rounded-3" placeholder="Ej: Ingeniería en Sistemas">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Sección</label>
                        <input type="text" name="Seccion" class="form-control rounded-3" placeholder="Ej: 01">
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Escuela</label>
                        <select name="IdEscuela" id="escuela" class="form-select rounded-3" required>
                            <option value="">Seleccione una escuela</option>
                            <option value="1">Informática</option>
                            <option value="2">Ciencias Aplicadas</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Materia</label>
                        <select name="Materia" id="materia" class="form-select rounded-3" required>
                            <option value="">Seleccione una materia</option>
                        </select>
                    </div>

                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Docente asignado</label>

                    <select name="Carnet" class="form-select rounded-3" required>
                        <option value="">Seleccione docente</option>

                        @foreach($docentes as $docente)
                            <option value="{{ $docente->Carnet }}">
                                {{ $docente->Nombres }} {{ $docente->Apellidos }} ({{ $docente->Carnet }})
                            </option>
                        @endforeach

                    </select>
                </div>
                <div class="d-grid">
                    <button class="btn btn-success rounded-3 py-2 fw-semibold">
                        💾 Guardar Investigación
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>
@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: "{{ session('success') }}",
            showConfirmButton: false,
            timer: 2000,
            timerProgressBar: true
        });
    });
</script>
@endif
<script>
    const materias = {
        1: ["Base de Datos", "Programación 1", "Redes"],
        2: ["Dibujo Técnico", "Física", "Matemática Aplicada"]
    };

    const escuelaSelect = document.getElementById('escuela');
    const materiaSelect = document.getElementById('materia');

    escuelaSelect.addEventListener('change', function () {
        const escuelaId = this.value;

        materiaSelect.innerHTML = '<option value="">Seleccione una materia</option>';

        if (materias[escuelaId]) {
            materias[escuelaId].forEach(function (materia) {
                let option = document.createElement('option');
                option.value = materia;
                option.text = materia;
                materiaSelect.appendChild(option);
            });
        }
    });
</script>


@endsection