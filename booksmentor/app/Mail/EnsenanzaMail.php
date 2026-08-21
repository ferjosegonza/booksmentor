<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Ensenanza;

class EnsenanzaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $usuario;
    public $libro;
    public $ensenanza;
    public $textosPorIdioma; // array of ['idioma' => CatIdioma, 'texto' => string]

    public function __construct(Usuario $usuario, Libro $libro, Ensenanza $ensenanza, array $textosPorIdioma)
    {
        $this->usuario = $usuario;
        $this->libro = $libro;
        $this->ensenanza = $ensenanza;
        $this->textosPorIdioma = $textosPorIdioma;
    }

    public function build()
    {
        $subject = "📖 BooksMentor — [{$this->libro->titulo}]: {$this->ensenanza->tema}";

        return $this->subject($subject)
                    ->view('emails.ensenanza');
    }
}