<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #9f1239; color: white; padding: 15px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Observaciones de Revisión</h2>
        </div>
        <div class="content">
            <p><strong>Documento:</strong> {{ $nombreDocumento }}</p>
            <p><strong>Observaciones:</strong></p>
            <div style="background: white; padding: 15px; border-left: 4px solid #9f1239;">
                {{ nl2br($mensaje) }}
            </div>
            <p style="margin-top: 20px;">Por favor revisar y realizar las correcciones correspondientes.</p>
        </div>
        <p style="text-align: center; color: #666; font-size: 0.9em;">
            Enviado desde el Sistema de Control de Investigaciones
        </p>
    </div>
</body>
</html>