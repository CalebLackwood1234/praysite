<?php
include_once 'config\configEmail.php';

include_once 'libs\PHPMailer\Exception.php';
include_once 'libs\PHPMailer\SMTP.php';
include_once 'libs\PHPMailer\PHPMailer.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Email {

    private $email;
    private $asunto;
    private $cuerpo;

    public function setEmail($email){ $this->email = $email; }
    public function setAsunto($asunto){ $this->asunto = $asunto; }
    public function setCuerpo($cuerpo){ $this->cuerpo = $cuerpo; }

    public function getEmail(){ return $this->email;}
    public function getAsunto(){ return $this->asunto;}
    public function getCuerpo(){ return $this->cuerpo;}


    public function generarEmail($tipo, $parametros = [], $valores = [], $asuntoAdd = ""){
        $this->asunto = constant($tipo . '_ASUNTO') . $asuntoAdd;

        if( $parametros == NULL || !is_array($parametros) ) {
            $parametros = [];
        }
        if( $valores == NULL || !is_array($valores) ) {
            $valores = [];
        }

        array_push($parametros, "{{asunto}}");
        array_push($valores, $this->asunto);

        $this->cuerpo = file_get_contents(constant($tipo . '_TEMPLATE'));
        $this->cuerpo = str_replace($parametros, $valores, $this->cuerpo);
    }

    public function envirEmail(){
		
		if( function_exists("fastcgi_finish_request") ) { // Verifico que la funcion exista
			fastcgi_finish_request(); // continuar la ejecucion en segundo plano
		}

		$mail = new PHPMailer();
		$mail->From = 'info@praysite.com';
		$mail->FromName = 'PraySite.com';
		$mail->AddAddress($this->email);
		$mail->Subject = $this->asunto;
		$mail->Body = $this->cuerpo;
		$mail->IsHTML(true);
		$mail->CharSet = 'UTF-8';
		$mail->Send();

    }

}

?>