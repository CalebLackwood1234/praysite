<?php
include_once 'libs/texto.php';
include_once 'libs/email.php';

if (!defined('INTENTOS_PASSWORD'))		define('INTENTOS_PASSWORD',			10);
if (!defined('ESTADO_SIN_VERIFICAR')) 	define('ESTADO_SIN_VERIFICAR',		0);
if (!defined('ESTADO_DESBLOQUEADO')) 	define('ESTADO_DESBLOQUEADO',		0);
if (!defined('TIPO_USUARIO_DEBOTO'))	define('TIPO_USUARIO_DEBOTO',		3);

class Deslogueado extends Controller{

    function __construct(){
        parent::__construct();
    }

    function registrar(){
        if($this->existPOST(['nick', 'nombre', 'email', 'pass', 'fechaNacimiento'])){
			
			if(caracteresInvalidos($this->getPost('nick')) || caracteresInvalidos($this->getPost('nombre')) || caracteresInvalidos($this->getPost('email')) || caracteresInvalidos($this->getPost('fechaNacimiento'))) {
				return new Errores(105);
			}
			
			if(emailInvalido($this->getPost('email'))){
				return new Errores(106);
			}
			
			if(lenghtInvalido($this->getPost('nick'), 3) || lenghtInvalido($this->getPost('nombre'), 3) || lenghtInvalido($this->getPost('pass'), 3)){
				return new Errores(107);
			}
			
			if(fechaInvalida($this->getPost('fechaNacimiento'))){
				return new Errores(108);
			}
			
			$this->model->borrarUsuarioSinVerificar(strtolower($this->getPost('nick')), strtolower($this->getPost('email')));
			
			$resultadoBuscar = $this->model->buscarUsuario(strtolower($this->getPost('nick')), strtolower($this->getPost('email')));
			if( $resultadoBuscar != false ) {
				return new Errores(111);
			}
			
			$semillaPass = encriptadoMd5Azar($this->getPost('nick') . $this->getPost('email'));
			$semillaSesion = encriptadoMd5Azar($this->getPost('nick') . $this->getPost('email'));
			$hashPass = encriptadoMd5($this->getPost('pass') . $semillaPass);
			$codigoVerificacion = encriptadoMd5Azar($this->getPost('nick') . $this->getPost('email'));
			
			$resultado = $this->model->registrar(
				strtolower($this->getPost('nick')),
				simplificar($this->getPost('nick')),
				$this->getPost('nombre'),
				simplificar($this->getPost('nombre')),
				strtolower($this->getPost('email')),
				$hashPass,
				$semillaPass,
				$semillaSesion,
				$codigoVerificacion,
				constant('ESTADO_SIN_VERIFICAR'),
				constant('ESTADO_DESBLOQUEADO'),
				constant('TIPO_USUARIO_DEBOTO'),
				$this->getPost('fechaNacimiento'));
			
			if ($this->checkError($resultado)) return;
			
			$email = new Email();
			
			$email->setEmail(strtolower($this->getPost('email')));
            $parametros = ["{{alias}}", "{{email}}", "{{codigo}}"];
            $valores = [$this->getPost('nombre'), strtolower($this->getPost('email')), $codigoVerificacion];
            $email->generarEmail("VERIFICAR_EMAIL", $parametros, $valores);
			
			//error_log("CODIGO_VERIFICACION:" . $codigoVerificacion);
			$email->envirEmail();
			
			return new Errores(701);
			
        } else {
            return new Errores(104);
        }
    }
	
	function verificarEmail() {
		if($this->existPOST(['email', 'codigo'])){
			
			if(emailInvalido($this->getPost('email'))){
				return new Errores(106);
			}
			
			$this->model->borrarUsuarioSinVerificarSinIntentos( strtolower($this->getPost('email')) );
			
			$resultadoBuscar = $this->model->getUsuarioEmail(strtolower($this->getPost('email')));
			
			if ($this->checkError($resultadoBuscar)) return;
			
			if (!is_array($resultadoBuscar)) {
				return new Errores(110);
			}
			
			if ( strcmp($resultadoBuscar['tipoCodigoVerificacion'], "R") != 0 ) return new Errores(126);
			
			if ( $resultadoBuscar['estaVerificado'] == 1 ) return new Errores(703);
			
			if ( strcmp($resultadoBuscar['codigoVerificacion'], $this->getPost('codigo')) == 0 ) {
				if( $this->model->setUsuarioVerificarEmail($resultadoBuscar['id']) ) {
					return new Errores(702);
				} else {
					return new Errores(114);
				}
			} else {
				$this->model->setUsuarioIntentos($resultadoBuscar['id'], $resultadoBuscar['intentos'] - 1);
				return new Errores(115);
			}
			
        } else {
            return new Errores(104);
        }
	}
	
	function solicitarVerificarEmail($resultadoBuscar) {
		$codigoVerificacion = encriptadoMd5Azar($resultadoBuscar['nick'] . $resultadoBuscar['email']);
		
		if ( $this->model->setUsuarioCodigoVerificacion($resultadoBuscar['id'], $codigoVerificacion, "R")) {
			$email = new Email();
			
			$email->setEmail($resultadoBuscar['email']);
            $parametros = ["{{alias}}", "{{email}}", "{{codigo}}"];
            $valores = [$resultadoBuscar['nombre'], $resultadoBuscar['email'], $codigoVerificacion];
            $email->generarEmail("VERIFICAR_EMAIL", $parametros, $valores);
			
			$email->envirEmail();
			
			return new Errores(701);
		} else {
			return new Errores(116);
		}
	}
	
	function solicitarBlanqueoPassword() {
		if($this->existPOST(['email'])){
			
			if(emailInvalido($this->getPost('email'))){
				return new Errores(106);
			}
			
			$resultadoBuscar = $this->model->getUsuarioEmail(strtolower($this->getPost('email')));
			
			if ($this->checkError($resultadoBuscar)) return;
			
			if (!is_array($resultadoBuscar)) {
				return new Errores(106);
			}
			
			if ( $resultadoBuscar['estaVerificado'] == 0 ) {
				return $this->solicitarVerificarEmail($resultadoBuscar);
			}
			
			$codigoVerificacion = encriptadoMd5Azar($resultadoBuscar['nick'] . $resultadoBuscar['email']);
			
			if ( !$this->model->setUsuarioCodigoVerificacion($resultadoBuscar['id'], $codigoVerificacion, "B")) {
				return new Errores(116);
			}
		
			$email = new Email();
			
			$email->setEmail($resultadoBuscar['email']);
            $parametros = ["{{alias}}", "{{email}}", "{{codigo}}"];
            $valores = [$resultadoBuscar['nombre'], $resultadoBuscar['email'], $codigoVerificacion];
            $email->generarEmail("BLANQUEAR_PASSWORD", $parametros, $valores);
			
			$email->envirEmail();
			
			return new Errores(705);
        } else {
            return new Errores(104);
        }
	}
	
	function blanquearPassword() {
		if($this->existPOST(['email', 'codigo', 'pass'])){
			
			if(emailInvalido($this->getPost('email'))){
				return new Errores(106);
			}
			
			if(lenghtInvalido($this->getPost('pass'), 3)){
				return new Errores(107);
			}
			
			$resultadoBuscar = $this->model->getUsuarioEmail(strtolower($this->getPost('email')));
			
			if ($this->checkError($resultadoBuscar)) return;
			
			if (!is_array($resultadoBuscar) || $resultadoBuscar['estaVerificado'] == 0 || $resultadoBuscar['codigoVerificacion'] == NULL || strcmp($resultadoBuscar['tipoCodigoVerificacion'], "B") != 0 ) {
				return new Errores(117);
			}
			
			if ( strcmp($resultadoBuscar['codigoVerificacion'], $this->getPost('codigo')) == 0 ) {
				
				
				$semillaPass = encriptadoMd5Azar($resultadoBuscar['nick'] . $resultadoBuscar['email']);
				$semillaSesion = encriptadoMd5Azar($resultadoBuscar['nick'] . $resultadoBuscar['email']);
				$hashPass = encriptadoMd5($this->getPost('pass') . $semillaPass);
				
				if( $this->model->setUsuarioPassword($resultadoBuscar['id'], $semillaPass, $semillaSesion, $hashPass) ) {
					return new Errores(706);
				} else {
					return new Errores(125);
				}
			} else {
				$this->model->setUsuarioCodigoVerificacion($resultadoBuscar['id'], NULL, NULL);
				return new Errores(117);
			}
			
        } else {
            return new Errores(104);
        }
	}
	
	function loguear() {
		if($this->existPOST(['email', 'pass'])){
			
			if(emailInvalido($this->getPost('email'))){
				return new Errores(120);
			}
			
			$resultadoBuscar = $this->model->getUsuarioEmail(strtolower($this->getPost('email')));
			
			if ($this->checkError($resultadoBuscar)) return;
			
			if (!is_array($resultadoBuscar)) {
				return new Errores(120);
			}
			
			if ( $resultadoBuscar['estaVerificado'] == 0 ) {
				return $this->solicitarVerificarEmail($resultadoBuscar);
			}
			
			$fecha = new DateTime();
			
			if ( $resultadoBuscar['intentos'] < 1 ) {
				if ( strcmp( $resultadoBuscar['fechaReinicioIntentos'], $fecha->format('Y-m-d H:i:s')) < 0) {
					$this->model->setUsuarioIntentos($resultadoBuscar['id'], constant('INTENTOS_PASSWORD'));
					$resultadoBuscar['intentos'] = constant('INTENTOS_PASSWORD');
					$this->model->setUsuarioFechaReinicioIntentos($resultadoBuscar['id'], null);
				} else {
					return new Errores(119);
				}
			}
			
			$hashPass = encriptadoMd5($this->getPost('pass') . $resultadoBuscar['semillaPass']);
			
			if ( strcmp($resultadoBuscar['hashPass'], $hashPass) != 0 ) {
				if ( $resultadoBuscar['intentos'] == 1 ) {
					$fecha->modify('+20 minutes');
					$this->model->setUsuarioFechaReinicioIntentos($resultadoBuscar['id'], $fecha->format('Y-m-d H:i:s'));
				}
				$this->model->setUsuarioIntentos($resultadoBuscar['id'], $resultadoBuscar['intentos'] - 1);
				return new Errores(120);
			} else {
				$this->model->setUsuarioIntentos($resultadoBuscar['id'], constant('INTENTOS_PASSWORD'));
				$this->model->setUsuarioCodigoVerificacion($resultadoBuscar['id'], NULL, NULL);
				
				$this->model->session->crearSession($resultadoBuscar['nick'], $resultadoBuscar['email'], $resultadoBuscar['id'], $resultadoBuscar['semillaSesion']);
				
				return new Errores(708);
			}
        } else {
            return new Errores(104);
        }
	}
	
	function cambiarEmail() {
		if($this->existPOST(['email', 'codigo'])){
			
			if(emailInvalido($this->getPost('email'))){
				return new Errores(106);
			}
			
			$resultadoBuscar = $this->model->getUsuarioEmail(strtolower($this->getPost('email')));
			
			if ($this->checkError($resultadoBuscar)) return;
			
			if (!is_array($resultadoBuscar)) {
				return new Errores(110);
			}
			
			if ( strcmp($resultadoBuscar['tipoCodigoVerificacion'], "E") != 0 ) return new Errores(126);
			
			if ($resultadoBuscar['estaVerificado'] == '0') return new Errores(127);
			
			$resultadoBuscarEmail = $this->model->getUsuarioEmail($resultadoBuscar['emailPropuesto']);
			if ($this->checkError($resultadoBuscarEmail)) return;
			if (is_array($resultadoBuscarEmail)) {
				return new Errores(112);
			}
			
			if ( strcmp($resultadoBuscar['codigoVerificacion'], $this->getPost('codigo')) == 0 ) {
				if( $this->model->setUsuarioVerificarEmail($resultadoBuscar['id']) ) {
					if( $this->model->setEmail($resultadoBuscar['id'], $resultadoBuscar['emailPropuesto']) ) {
					
					} else {
						return new Errores(125);
					}
				} else {
					return new Errores(125);
				}
			} else {
				$this->model->setUsuarioIntentos($resultadoBuscar['id'], $resultadoBuscar['intentos'] - 1);
				return new Errores(115);
			}
			
        } else {
            return new Errores(104);
        }
	}
	
	function getTyc() {
		$resultadoBuscar = $this->model->getTyc();
		
		if ($this->checkError($resultadoBuscar)) return;
		
		return $this->view->render($resultadoBuscar, 200);
	}

}

?>
