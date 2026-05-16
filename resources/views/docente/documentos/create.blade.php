@extends('layouts.app-docente')

@section('page_title')
    Subir Documentos
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Subir Documentos</h2>
    <a href="{{ route('docente.investigaciones.index') }}" class="btn btn-secondary">
        ← Volver a la lista
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header text-white" style="background-color: #9f1239;">
        <strong>Investigación:</strong> {{ $investigacion->Titulo }}
    </div>
    <div class="card-body">
        <p><strong>Carrera:</strong> {{ $investigacion->Carrera }} | 
           <strong>Materia:</strong> {{ $investigacion->Materia }} | 
           <strong>Sección:</strong> {{ $investigacion->Seccion }}</p>
    </div>
</div>

<div class="mt-4">
    <h4>Subir nuevo documento</h4>

    <!-- 🔥 MENSAJES -->
    @if(session('success'))
        <div class="alert alert-success mt-3">
            ✅ {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger mt-3">
            ❌ {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            ❌ Hubo errores:
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ url('/docente/documentos/store') }}" method="POST" enctype="multipart/form-data" id="formSubida">
        @csrf

        <input type="hidden" name="IdInvestigacion" value="{{ $investigacion->IdInvestigacion }}">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Tipo de Entrega</label>
                <select name="tipo_entrega" id="tipoEntrega" class="form-select" required>
                    <option value="avance_1">Avance 1</option>
                    <option value="avance_2">Avance 2</option>
                    <option value="avance_3">Avance 3</option>
                    <option value="final">Documento Final</option>
                    <option value="banner">Banner</option>
                    <option value="extra">Documento Extra</option>
                </select>
            </div>
        </div>

        <!-- Banner -->
        <div class="mb-3" id="campoBanner" style="display: none;">
            <label class="form-label">Subir Banner</label>
            <input type="file" name="banner" class="form-control" accept=".jpg,.jpeg,.png">
        </div>

        <div class="mb-3">
            <label class="form-label">Seleccionar archivo principal</label>
            <input type="file" name="archivos[]" class="form-control" multiple accept=".pdf,.doc,.docx,.jpg,.png">
        </div>

        <div class="mb-3">
            <label class="form-label">Comentario (opcional)</label>
            <textarea name="Comentario" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-success btn-lg" id="btnSubir">
            📤 Subir Documento
        </button>
    </form>
</div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    const tipo = document.getElementById('tipoEntrega');
    const banner = document.getElementById('campoBanner');

    tipo.addEventListener('change', function() {
        banner.style.display = (this.value === 'final') ? 'block' : 'none';
    });

    const form = document.getElementById('formSubida');
    const btn = document.getElementById('btnSubir');

    form.addEventListener('submit', function(e) {

        const inputFile = document.querySelector('input[name="archivos[]"]');

        if (!inputFile.files.length) {
            e.preventDefault();
            alert("⚠️ Selecciona al menos un archivo.");
            return;
        }

        const maxSize = 10 * 1024 * 1024;

        for (let file of inputFile.files) {
            if (file.size > maxSize) {
                e.preventDefault();
                alert("❌ " + file.name + " supera 10MB.");
                return;
            }
        }

        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Subiendo...';
        btn.disabled = true;
    });

});
</script>
@endsection