<?php
include_once 'libs/texto.php';
include_once 'libs/email.php';
include_once 'libs/imagen.php';

if (!defined('INTENTOS_PASSWORD'))			define('INTENTOS_PASSWORD',			10);

class Logueado extends Controller{

    function __construct(){
        parent::__construct();
    }
	
	function desloguear() {
		
		$resultadoSesion = $this->model->session->obtenerSession();
		
		if ($this->checkError($resultadoSesion)) return;

		if (! $this->model->session->cerrarSession( $resultadoSesion['idSesion'], $resultadoSesion['idUsuario'] ) ) return new Errores(123);
		
		return new Errores(707);
	}
	
	function checkear() {
		
		sleep(1);
		
		$resultadoSesion = $this->model->session->obtenerSession();
		
		if ($this->checkError($resultadoSesion)) return;
		
		return $this->view->render($resultadoSesion, 200);
	}
	
	function setNombre() {
		try{
			if($this->existPOST(['nombre'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				if(caracteresInvalidos($this->getPost('nombre'))) return new Errores(105);
				
				if( $this->model->setNombre($resultadoSesion['idUsuario'], $this->getPost('nombre'), simplificar($this->getPost('nombre'))) ) {
					return $this->view->render("", 200);
				} else {
					return new Errores(125);
				}
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function setClave() {
		try{
			if($this->existPOST(['claveAnterio', 'claveNueva'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				$resultadoBuscar = $this->model->getUsuarioEmail($resultadoSesion['email']);
				if ($this->checkError($resultadoBuscar)) return;
				if (!is_array($resultadoBuscar)) {
					return new Errores(132);
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
				
				$hashPass = encriptadoMd5($this->getPost('claveAnterio') . $resultadoBuscar['semillaPass']);
				
				if ( strcmp($resultadoBuscar['hashPass'], $hashPass) != 0 ) {
					if ( $resultadoBuscar['intentos'] == 1 ) {
						$fecha->modify('+20 minutes');
						$this->model->setUsuarioFechaReinicioIntentos($resultadoBuscar['id'], $fecha->format('Y-m-d H:i:s'));
					}
					$this->model->setUsuarioIntentos($resultadoBuscar['id'], $resultadoBuscar['intentos'] - 1);
					return new Errores(150);
				} else {
					$semillaPass = encriptadoMd5Azar($resultadoBuscar['nick'] . $resultadoBuscar['email']);
					$semillaSesion = encriptadoMd5Azar($resultadoBuscar['nick'] . $resultadoBuscar['email']);
					$hashPass = encriptadoMd5($this->getPost('claveNueva') . $semillaPass);
					
					if( $this->model->setUsuarioPassword($resultadoBuscar['id'], $semillaPass, $semillaSesion, $hashPass) ) {
						$this->model->session->cerrarSession( $resultadoSesion['idSesion'], $resultadoSesion['idUsuario'] );
						
						return new Errores(707);
					} else {
						return new Errores(125);
					}
				}
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function cambiarEmail() {
		try{
			if($this->existPOST(['nuevoEmail'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				if(emailInvalido($this->getPost('nuevoEmail'))) return new Errores(105);
				
				if( $this->model->setUsuarioEmailPropuesto($resultadoSesion['idUsuario'], strtolower($this->getPost('nuevoEmail'))) ) {
					$codigoVerificacion = encriptadoMd5Azar($resultadoSesion['nick'] . strtolower($this->getPost('nuevoEmail')));

					if ( !$this->model->setUsuarioCodigoVerificacion($resultadoSesion['idUsuario'], $codigoVerificacion, "E")) {
						return new Errores(116);
					}

					$email = new Email();

					$email->setEmail(strtolower($this->getPost('nuevoEmail')));
					$parametros = ["{{alias}}", "{{nick}}", "{{email}}", "{{codigo}}"];
					$valores = [$resultadoSesion['nombre'], $resultadoSesion['nick'], $resultadoSesion['email'], $codigoVerificacion];
					$email->generarEmail("CAMBIAR_EMAIL", $parametros, $valores);

					$email->envirEmail();
					
					return $this->view->render("", 200);
				} else {
					return new Errores(125);
				}
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function cambiarImagen() {
		try{
			$resultadoSesion = $this->model->session->obtenerSession();
			if ($this->checkError($resultadoSesion)) return;
			
			$archivo = $_FILES['archivo']['name'];
			if (isset($archivo) && $archivo != "") {
				$tipo = $_FILES['archivo']['type'];
				$tamano = $_FILES['archivo']['size'];
				$temp = $_FILES['archivo']['tmp_name'];
				
				if ( !esFormatoValido($tipo) ) {
					return new Errores(143);
				} else if ( !esSizeValido($tamano) ){
					return new Errores(144);
				} else {
					$fecha = new DateTime();
					$urlImagen = $fecha->format('Y-m-d') . encriptadoMd5Azar($resultadoSesion['nick']);
					$urlCarpetaFinal = constant('UBICACION_IMAGENES_PERFIL') . $resultadoSesion['idUsuario'];
					$urlImagenFinal = $urlCarpetaFinal . '/' . $urlImagen . '.jpg';
					$urlImagenFinalMed = $urlCarpetaFinal . '/' . $urlImagen . '_med.jpg';
					$urlImagenFinalMini = $urlCarpetaFinal . '/' . $urlImagen . '_mini.jpg';
					
					crearCarpeta($urlCarpetaFinal);
					
					if (moverArchivo($temp, $urlImagenFinal)) {
						
						resizeImagenMax($urlImagenFinal, constant('MAXIMO_ANCHO_PUBLICACION'), constant('MAXIMO_ALTO_PUBLICACION'));
						copiarArchivo($urlImagenFinal, $urlImagenFinalMed);
						copiarArchivo($urlImagenFinal, $urlImagenFinalMini);
						resizeToShortSide($urlImagenFinalMed,  constant('MAXIMO_PERFIL_MED'));
						resizeToShortSide($urlImagenFinalMini, constant('MAXIMO_PERFIL_MINI'));
						
						if( $this->model->setUsuarioUrlImagen($resultadoSesion['idUsuario'], $urlImagen) ) {
							if( $resultadoSesion['urlImagen'] != null && $resultadoSesion['urlImagen'] != "" ) {
								borrarArchivo($urlCarpetaFinal . '/' . $resultadoSesion['urlImagen'] . '.jpg');
								borrarArchivo($urlCarpetaFinal . '/' . $resultadoSesion['urlImagen'] . '_med.jpg');
								borrarArchivo($urlCarpetaFinal . '/' . $resultadoSesion['urlImagen'] . '_mini.jpg');
							}
							return $this->view->render($urlImagen, 200);
						} else {
							return new Errores(145);
						}
					} else {
						return new Errores(145);
					}
				}
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(145);
		}
	}

}

?>
