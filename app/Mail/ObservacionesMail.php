<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ObservacionesMail extends Mailable
{
    use Queueable, SerializesModels;

    public $asunto;
    public $mensaje;
    public $nombreDocumento;

    public function __construct($asunto, $mensaje, $nombreDocumento)
    {
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->nombreDocumento = $nombreDocumento;
    }

    public function build()
    {
        return $this->subject($this->asunto)
                    ->view('emails.observaciones')
                    ->with([
                        'asunto' => $this->asunto,
                        'mensaje' => $this->mensaje,
                        'nombreDocumento' => $this->nombreDocumento,
                    ]);
    }
}