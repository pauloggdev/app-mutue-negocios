<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailCancelarLicenca extends Mailable
{
    use Queueable, SerializesModels;

    private $data;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $this->subject($this->data['assunto']);
        $this->to($this->data['emailEmpresa']);
        
        return $this->view('mail.cancelarPedidoLicenca', $this->data);
    }
}
