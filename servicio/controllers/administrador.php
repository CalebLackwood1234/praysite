<?php
include_once 'libs/texto.php';
include_once 'libs/email.php';

if (!defined('TIPO_USUARIO_ADMINISTRADOR')) 	define('TIPO_USUARIO_ADMINISTRADOR',	'1');
if (!defined('TIPO_USUARIO_LIDER')) 			define('TIPO_USUARIO_LIDER',			'2');
if (!defined('TIPO_USUARIO_DEBOTO')) 			define('TIPO_USUARIO_DEBOTO',			'3');

if (!defined('TIPO_RELACION_CHATS_HABILITADA')) define('TIPO_RELACION_CHATS_HABILITADA', 2);
if (!defined('LIDER_DESHABILITADO')) 			define('LIDER_DESHABILITADO', 0);
if (!defined('LIDER_HABILITADO')) 				define('LIDER_HABILITADO', 1);

if (!defined('TIPO_SUSCRIPCION_GRATIS')) 	define('TIPO_SUSCRIPCION_GRATIS', 1);
if (!defined('TIPO_SUSCRIPCION_OCULTA')) 	define('TIPO_SUSCRIPCION_OCULTA', 3);
if (!defined('NOMBRE_GRATIS')) 				define('NOMBRE_GRATIS', 'Gratis');
if (!defined('NOMBRE_OCULTA')) 				define('NOMBRE_OCULTA', 'Oculta');

if (!defined('ESTADO_DENUNCIA_NUEVA')) 		define('ESTADO_DENUNCIA_NUEVA',			1);
if (!defined('ESTADO_DENUNCIA_LEIDA')) 		define('ESTADO_DENUNCIA_LEIDA',			2);
if (!defined('ESTADO_DENUNCIA_ACEPTADA')) 	define('ESTADO_DENUNCIA_ACEPTADA',		3);
if (!defined('ESTADO_DENUNCIA_RECHAZADA')) 	define('ESTADO_DENUNCIA_RECHAZADA',		4);

class Administrador extends Controller{

    function __construct(){
        parent::__construct();
    }

	function listarUsuarios() {
		try{
			if($this->existPOST(['pagina'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				$inicio = (((int) $this->getPost('pagina')) -1) * constant('ELEMENTOS_POR_PAGINA');
				$filtros = "WHERE 1";
				
				if($this->existPOST(['tipoUsuario']) && $this->getPost('tipoUsuario') != '') {
					if($this->getPost('tipoUsuario') != constant('TIPO_USUARIO_ADMINISTRADOR') && $this->getPost('tipoUsuario') != constant('TIPO_USUARIO_LIDER') && $this->getPost('tipoUsuario') != constant('TIPO_USUARIO_DEBOTO')) return new Errores(105);
					$filtros .= " AND u.tipoUsuario = " . $this->getPost('tipoUsuario');
				}
				
				if($this->existPOST(['estaVerificado']) && $this->getPost('estaVerificado') != '') {
					if($this->getPost('estaVerificado') != '0' && $this->getPost('estaVerificado') != '1') return new Errores(105);
					$filtros .= " AND u.estaVerificado = " . $this->getPost('estaVerificado');
				}
				
				if($this->existPOST(['estaBloqueado']) && $this->getPost('estaBloqueado') != '') {
					if($this->getPost('estaBloqueado') != '0' && $this->getPost('estaBloqueado') != '1') return new Errores(105);
					$filtros .= " AND u.estaBloqueado = " . $this->getPost('estaBloqueado');
				}
				
				if($this->existPOST(['fechaCreacionDesde']) && $this->getPost('fechaCreacionDesde') != '') {
					if(caracteresInvalidos($this->getPost('fechaCreacionDesde'))) return new Errores(105);
					$filtros .= " AND u.fechaAlta > '" . $this->getPost('fechaCreacionDesde') . "'";
				}
				
				if($this->existPOST(['fechaCreacionHasta']) && $this->getPost('fechaCreacionHasta') != '') {
					if(caracteresInvalidos($this->getPost('fechaCreacionHasta'))) return new Errores(105);
					$filtros .= " AND u.fechaAlta < '" . $this->getPost('fechaCreacionHasta') . " 23:59:59'";
				}
				
				if($this->existPOST(['email']) && $this->getPost('email') != '') {
					if(caracteresInvalidos($this->getPost('email'))) return new Errores(105);
					$filtros .= " AND u.email like '%" . strtolower($this->getPost('email')) . "%'";
				}
				
				if($this->existPOST(['nick']) && $this->getPost('nick') != '') {
					if(caracteresInvalidos($this->getPost('nick'))) return new Errores(105);
					$filtros .= " AND u.nickSimplificado like '%" . simplificar($this->getPost('nick')) . "%'";
				}
				
				if($this->existPOST(['nombre']) && $this->getPost('nombre') != '') {
					if(caracteresInvalidos($this->getPost('nombre'))) return new Errores(105);
					$filtros .= " AND u.nombreSimplificado like '%" . simplificar($this->getPost('nombre')) . "%'";
				}
				
				$resultado = $this->model->listarUsuarios($inicio,constant('ELEMENTOS_POR_PAGINA'),$filtros);
					
				return $this->view->render($resultado, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function getUsuario() {
		try{
			if($this->existPOST(['idUsuario'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idUsuario'))) return new Errores(105);
				
				$resultadoBuscarId = $this->model->getUsuarioId($this->getPost('idUsuario'));
				if ($this->checkError($resultadoBuscarId)) return;
				if (!is_array($resultadoBuscarId)) {
					return new Errores(110);
				}
				
				$resultadoBuscarPaginas = $this->model->listarPaginasPorLider($this->getPost('idUsuario'));
				if ($this->checkError($resultadoBuscarPaginas)) return;
				if (!is_array($resultadoBuscarPaginas)) {
					return new Errores(110);
				}
				
				$resultadoBuscarId['paginas'] = $resultadoBuscarPaginas;
				
				return $this->view->render($resultadoBuscarId, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function setTipoUsuario() {
		try{
			if($this->existPOST(['idUsuario', 'tipoUsuario'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idUsuario'))) return new Errores(105);
				if($this->getPost('tipoUsuario') != constant('TIPO_USUARIO_LIDER') && $this->getPost('tipoUsuario') != constant('TIPO_USUARIO_DEBOTO')) return new Errores(105);
				
				if( $this->model->setTipoUsuario($this->getPost('idUsuario'), $this->getPost('tipoUsuario')) ) {
					$tipoUsuarioNombre = "";
					$resultadoBuscarId = $this->model->getUsuarioLiderId($this->getPost('idUsuario'));
					
					if($this->getPost('tipoUsuario') == constant('TIPO_USUARIO_LIDER')) {
						$tipoUsuarioNombre = "Lider";
						if ($this->checkError($resultadoBuscarId)) return;
						if (!is_array($resultadoBuscarId)) {
							return new Errores(110);
						}
						if ($resultadoBuscarId['biografia'] == null) {
							if(! $this->model->addLider($this->getPost('idUsuario'), 'Nuevo Lider', constant('LIDER_HABILITADO'), constant('TIPO_RELACION_CHATS_HABILITADA')) ) {
								return new Errores(125);
							}
						} else {
							if(! $this->model->setLiderHabilitar($this->getPost('idUsuario'), constant('LIDER_HABILITADO')) ) {
								return new Errores(125);
							}
						}
						$resultadoSuscripcionHabilitada = $this->model->getSuscripcionHabilitada($this->getPost('idUsuario'), constant('TIPO_SUSCRIPCION_GRATIS'));
						if ($this->checkError($resultadoSuscripcionHabilitada)) return;
						if (!is_array($resultadoSuscripcionHabilitada)) {
							if(! $this->model->addSuscripcionHabilitada($this->getPost('idUsuario'), constant('TIPO_SUSCRIPCION_GRATIS'), constant('NOMBRE_GRATIS')) ) {
								return new Errores(125);
							}
						}
						$resultadoSuscripcionHabilitada = $this->model->getSuscripcionHabilitada($this->getPost('idUsuario'), constant('TIPO_SUSCRIPCION_OCULTA'));
						if ($this->checkError($resultadoSuscripcionHabilitada)) return;
						if (!is_array($resultadoSuscripcionHabilitada)) {
							if(! $this->model->addSuscripcionHabilitada($this->getPost('idUsuario'), constant('TIPO_SUSCRIPCION_OCULTA'), constant('NOMBRE_OCULTA')) ) {
								return new Errores(125);
							}
						}
					} else if($this->getPost('tipoUsuario') == constant('TIPO_USUARIO_DEBOTO')) {
						$tipoUsuarioNombre = "Deboto";
						
						$resultadoModificarPaginas = $this->model->setPaginasBloqueo($this->getPost('idUsuario'), 1);
						
						if ($this->checkError($resultadoBuscarId)) return;
						if (!is_array($resultadoBuscarId)) {
							return new Errores(110);
						}
						if ($resultadoBuscarId['biografia'] != null) {
							if(! $this->model->setLiderHabilitar($this->getPost('idUsuario'), constant('LIDER_DESHABILITADO')) ) {
								return new Errores(125);
							}
						}
					} else {
						return new Errores(104);
					}
					
					$email = new Email();

					$email->setEmail($resultadoBuscarId['email']);
					$parametros = ["{{alias}}", "{{tipoUsuario}}"];
					$valores = [$resultadoBuscarId['nombre'], $tipoUsuarioNombre];
					$email->generarEmail("CAMBIO_TIPO_USUARIO", $parametros, $valores);

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
	
	function setEstaBloqueado() {
		try{
			if($this->existPOST(['idUsuario', 'estaBloqueado'])){
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idUsuario'))) return new Errores(105);
				if($this->getPost('estaBloqueado') != '0' && $this->getPost('estaBloqueado') != '1') return new Errores(105);
				
				if( $this->model->setEstaBloqueado($this->getPost('idUsuario'), $this->getPost('estaBloqueado')) ) {
					
					$resultadoBuscarId = $this->model->getUsuarioLiderId($this->getPost('idUsuario'));
					
					if($this->getPost('estaBloqueado') == '1') {
						$resultadoModificarPaginas = $this->model->setPaginasBloqueo($this->getPost('idUsuario'), $this->getPost('estaBloqueado'));
						
						$email = new Email();

						$email->setEmail(strtolower($resultadoBuscarId['email']));
						$parametros = ["{{alias}}", "{{nick}}"];
						$valores = [$resultadoBuscarId['nombre'], $resultadoBuscarId['nick']];
						$email->generarEmail("USUARIO_BLOQUEADO", $parametros, $valores);

						$email->envirEmail();
						
						return $this->view->render("", 200);
					} else {
						$email = new Email();

						$email->setEmail(strtolower($resultadoBuscarId['email']));
						$parametros = ["{{alias}}", "{{nick}}"];
						$valores = [$resultadoBuscarId['nombre'], $resultadoBuscarId['nick']];
						$email->generarEmail("USUARIO_DESBLOQUEADO", $parametros, $valores);

						$email->envirEmail();
						
						return $this->view->render("", 200);
					}
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
	
	function setPaginaEstaBloqueado() {
		try{
			if($this->existPOST(['idUsuario', 'idPagina', 'estaBloqueado'])){
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idUsuario'))) return new Errores(105);
				if(caracteresInvalidos($this->getPost('idPagina'))) return new Errores(105);
				if($this->getPost('estaBloqueado') != '0' && $this->getPost('estaBloqueado') != '1') return new Errores(105);
				
				if( $this->model->setPaginaIdBloqueo($this->getPost('idUsuario'), $this->getPost('idPagina'), $this->getPost('estaBloqueado')) ) {
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
	
	function recuperarCuentaEmail() {
		try{
			if($this->existPOST(['idUsuario', 'nuevoEmail', 'verificar'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idUsuario'))) return new Errores(105);
				if(emailInvalido($this->getPost('nuevoEmail'))) return new Errores(105);
				if($this->getPost('verificar') != '0' && $this->getPost('verificar') != '1') return new Errores(105);
				
				$resultadoBuscarId = $this->model->getUsuarioId($this->getPost('idUsuario'));
				if ($this->checkError($resultadoBuscarId)) return;
				if (!is_array($resultadoBuscarId)) {
					return new Errores(110);
				}
				if ($resultadoBuscarId['estaVerificado'] == '0') {
					return new Errores(127);
				}
				if ($resultadoBuscarId['tipoUsuario'] == constant('TIPO_USUARIO_ADMINISTRADOR')) {
					return new Errores(128);
				}
				
				$resultadoBuscarEmail = $this->model->getUsuarioEmail(strtolower($this->getPost('nuevoEmail')));
				if ($this->checkError($resultadoBuscarEmail)) return;
				if (is_array($resultadoBuscarEmail)) {
					return new Errores(112);
				}
				
				if($this->getPost('verificar') == '0') { // Sin verificacion de email
					if( $this->model->setEmail($this->getPost('idUsuario'), strtolower($this->getPost('nuevoEmail'))) ) {
						if( $this->model->setUsuarioVerificarEmail($this->getPost('idUsuario')) ) {
							
							$email = new Email();

							$email->setEmail(strtolower($this->getPost('nuevoEmail')));
							$parametros = ["{{alias}}"];
							$valores = [$resultadoBuscarId['nombre']];
							$email->generarEmail("ADMINISTRADOR_CAMBIO_EMAIL", $parametros, $valores);

							$email->envirEmail();
							
							return $this->view->render("", 200);
						} else {
							return new Errores(125);
						}
					} else {
						return new Errores(125);
					}
				} else {
					if( $this->model->setUsuarioEmailPropuesto($this->getPost('idUsuario'), strtolower($this->getPost('nuevoEmail'))) ) {
						$codigoVerificacion = encriptadoMd5Azar($resultadoBuscarId['nick'] . strtolower($this->getPost('nuevoEmail')));

						if ( !$this->model->setUsuarioCodigoVerificacion($this->getPost('idUsuario'), $codigoVerificacion, "E")) {
							return new Errores(116);
						}

						$email = new Email();

						$email->setEmail(strtolower($this->getPost('nuevoEmail')));
						$parametros = ["{{alias}}", "{{nick}}", "{{email}}", "{{codigo}}"];
						$valores = [$resultadoBuscarId['nombre'], $resultadoBuscarId['nick'], $resultadoBuscarId['email'], $codigoVerificacion];
						$email->generarEmail("CAMBIAR_EMAIL", $parametros, $valores);

						$email->envirEmail();
						
						return $this->view->render("", 200);
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
	
	function recuperarCuentaPassword() {
		try{
			if($this->existPOST(['idUsuario', 'nuevoPassword'])){
				
				// DEBERIA MANDAR UN EMAIL
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idUsuario'))) return new Errores(105);

				if(lenghtInvalido($this->getPost('nuevoPassword'), 3)){
					return new Errores(107);
				}
				
				$resultadoBuscarId = $this->model->getUsuarioId($this->getPost('idUsuario'));
				if ($this->checkError($resultadoBuscarId)) return;
				if (!is_array($resultadoBuscarId)) {
					return new Errores(110);
				}
				if ($resultadoBuscarId['estaVerificado'] == '0') {
					return new Errores(127);
				}
				if ($resultadoBuscarId['tipoUsuario'] == constant('TIPO_USUARIO_ADMINISTRADOR')) {
					return new Errores(128);
				}
				
				$semillaPass = encriptadoMd5Azar($resultadoBuscarId['nick'] . $resultadoBuscarId['email']);
				$semillaSesion = encriptadoMd5Azar($resultadoBuscarId['nick'] . $resultadoBuscarId['email']);
				$hashPass = encriptadoMd5($this->getPost('nuevoPassword') . $semillaPass);
				
				if( $this->model->setUsuarioPassword($this->getPost('idUsuario'), $semillaPass, $semillaSesion, $hashPass) ) {
					
					$email = new Email();

					$email->setEmail(strtolower($resultadoBuscarId['email']));
					$parametros = ["{{alias}}"];
					$valores = [$resultadoBuscarId['nombre']];
					$email->generarEmail("ADMINISTRADOR_CAMBIO_PASSWORD", $parametros, $valores);

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
	
	function listarPaginas() {
		try{
			if($this->existPOST(['idLider'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				$resultado = $this->model->listarPaginasPorLider($this->getPost('idLider'));
					
				return $this->view->render($resultado, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function setBloqueoPaginas() {
		try{
			if($this->existPOST(['idPagina', 'bloqueo'])){
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('bloqueo'))) return new Errores(105);
				
				$resultadoBuscarPagina = $this->model->getPagina($this->getPost('idPagina'));
				if( !$resultadoBuscarPagina ) {
					return new Errores(125);
				}
				
				if( $this->model->setBloqueoPagina($this->getPost('idPagina'), $this->getPost('bloqueo')) ) {
					
					if( $this->getPost('bloqueo') == "1" ) {
						$email = new Email();

						$email->setEmail(strtolower($resultadoBuscarPagina['email']));
						$parametros = ["{{alias}}", "{{nombrePagina}}"];
						$valores = [$resultadoBuscarPagina['nombreUsuario'], $resultadoBuscarPagina['nombre']];
						$email->generarEmail("PAGINA_BLOQUEADA", $parametros, $valores);

						$email->envirEmail();
					} else {
						$email = new Email();

						$email->setEmail(strtolower($resultadoBuscarPagina['email']));
						$parametros = ["{{alias}}", "{{nombrePagina}}"];
						$valores = [$resultadoBuscarPagina['nombreUsuario'], $resultadoBuscarPagina['nombre']];
						$email->generarEmail("PAGINA_DESBLOQUEADA", $parametros, $valores);

						$email->envirEmail();
					}
					
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
	
	function listarTipoSuscripcion() {
		try{
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				$resultadoTipoSuscripcion = $this->model->listarTipoSuscripcion();
				
				if ($this->checkError($resultadoTipoSuscripcion)) return;
				
				return $this->view->render($resultadoTipoSuscripcion, 200);
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function addTipoSuscripcion() {
		try{
			if($this->existPOST(['nivel', 'nombre', 'precio', 'descripcion'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('nivel')) || caracteresInvalidos($this->getPost('nombre')) || caracteresInvalidos($this->getPost('precio')) || caracteresInvalidos($this->getPost('descripcion'))) {
					return new Errores(105);
				}
				
				if(lenghtInvalido($this->getPost('nombre'), 3) || lenghtInvalido($this->getPost('descripcion'), 3)){
					return new Errores(107);
				}
				
				if(strlen($this->getPost('nombre')) > constant('MAXIMO_CARACTERES_NOMBRE_CORTO')) return new Errores(133);
				if(strlen($this->getPost('descripcion')) > constant('MAXIMO_CARACTERES_DESCRIPCION_CORTA')) return new Errores(133);
				
				if( $this->model->addTipoSuscripcion($this->getPost('nivel'), $this->getPost('nombre'), $this->getPost('precio'), $this->getPost('descripcion')) ) {
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
	
	function modificarTipoSuscripcion() {
		try{
			if($this->existPOST(['idSuscripcion', 'nombre', 'precio', 'descripcion'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idSuscripcion')) || caracteresInvalidos($this->getPost('nombre')) || caracteresInvalidos($this->getPost('precio')) || caracteresInvalidos($this->getPost('descripcion'))) {
					return new Errores(105);
				}
				
				if(lenghtInvalido($this->getPost('nombre'), 3) || lenghtInvalido($this->getPost('descripcion'), 3)){
					return new Errores(107);
				}
				
				if(strlen($this->getPost('nombre')) > constant('MAXIMO_CARACTERES_NOMBRE_CORTO')) return new Errores(133);
				if(strlen($this->getPost('descripcion')) > constant('MAXIMO_CARACTERES_DESCRIPCION_CORTA')) return new Errores(133);
				
				if( $this->model->modificarTipoSuscripcion($this->getPost('idSuscripcion'), $this->getPost('nombre'), $this->getPost('precio'), $this->getPost('descripcion')) ) {
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
	
	function eliminarTipoSuscripcion() {
		try{
			if($this->existPOST(['idSuscripcion'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idSuscripcion'))) {
					return new Errores(105);
				}
				
				if ($this->model->eliminarTipoSuscripcion($this->getPost('idSuscripcion'))) {
					return $this->view->render('', 200);
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

	function listarDenuncias() {
		try{
			if($this->existPOST(['pagina'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				$inicio = (((int) $this->getPost('pagina')) -1) * constant('ELEMENTOS_POR_PAGINA');
				$filtros = "WHERE 1";
				
				if($this->existPOST(['tipoUsuarioDenunciante']) && $this->getPost('tipoUsuarioDenunciante') != '') {
					if(caracteresInvalidos($this->getPost('tipoUsuarioDenunciante'))) return new Errores(105);
					$filtros .= " AND u1.tipoUsuario = " . $this->getPost('tipoUsuarioDenunciante');
				}
				
				if($this->existPOST(['tipoUsuarioDenunciado']) && $this->getPost('tipoUsuarioDenunciado') != '') {
					if(caracteresInvalidos($this->getPost('tipoUsuarioDenunciado'))) return new Errores(105);
					$filtros .= " AND u2.tipoUsuario = " . $this->getPost('tipoUsuarioDenunciado');
				}
				
				if($this->existPOST(['estadoDenuncia']) && $this->getPost('estadoDenuncia') != '') {
					if(caracteresInvalidos($this->getPost('estadoDenuncia'))) return new Errores(105);
					$filtros .= " AND d.estado = " . $this->getPost('estadoDenuncia');
				}
				
				if($this->existPOST(['idUsuarioDenunciante']) && $this->getPost('idUsuarioDenunciante') != '') {
					if(caracteresInvalidos($this->getPost('idUsuarioDenunciante'))) return new Errores(105);
					$filtros .= " AND u1.id = " . $this->getPost('idUsuarioDenunciante');
				}
				
				if($this->existPOST(['idUsuarioDenunciado']) && $this->getPost('idUsuarioDenunciado') != '') {
					if(caracteresInvalidos($this->getPost('idUsuarioDenunciado'))) return new Errores(105);
					$filtros .= " AND u2.id = " . $this->getPost('idUsuarioDenunciado');
				}
				
				if($this->existPOST(['idUsuarioResolvio']) && $this->getPost('idUsuarioResolvio') != '') {
					if(caracteresInvalidos($this->getPost('idUsuarioResolvio'))) return new Errores(105);
					$filtros .= " AND d.resolvio = " . $this->getPost('idUsuarioResolvio');
				}
				
				if($this->existPOST(['fechaCreacionDesde']) && $this->getPost('fechaCreacionDesde') != '') {
					if(caracteresInvalidos($this->getPost('fechaCreacionDesde'))) return new Errores(105);
					$filtros .= " AND d.fechaAlta > '" . $this->getPost('fechaCreacionDesde') . "'";
				}
				
				if($this->existPOST(['fechaCreacionHasta']) && $this->getPost('fechaCreacionHasta') != '') {
					if(caracteresInvalidos($this->getPost('fechaCreacionHasta'))) return new Errores(105);
					$filtros .= " AND d.fechaAlta < '" . $this->getPost('fechaCreacionHasta') . " 23:59:59'";
				}
				
				if($this->existPOST(['fechaResolucionDesde']) && $this->getPost('fechaResolucionDesde') != '') {
					if(caracteresInvalidos($this->getPost('fechaResolucionDesde'))) return new Errores(105);
					$filtros .= " AND d.fechaAlta > '" . $this->getPost('fechaResolucionDesde') . "'";
				}
				
				if($this->existPOST(['fechaResolucionHasta']) && $this->getPost('fechaResolucionHasta') != '') {
					if(caracteresInvalidos($this->getPost('fechaResolucionHasta'))) return new Errores(105);
					$filtros .= " AND d.fechaResolucion < '" . $this->getPost('fechaResolucionHasta') . " 23:59:59'";
				}
				
				$resultadoDenuncias = $this->model->listarDenuncias($inicio,constant('ELEMENTOS_POR_PAGINA'),$filtros);
					
				return $this->view->render($resultadoDenuncias, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function leerDenuncia() {
		try{
			if($this->existPOST(['denuncia'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('denuncia'))) return new Errores(105);
				
				$resultadoDenuncia = $this->model->leerDenuncia($this->getPost('denuncia'));
				if(!$resultadoDenuncia) {
					return new Errores(132);
				}
				
				if( $resultadoDenuncia['estado'] == constant('ESTADO_DENUNCIA_NUEVA') ) {
					$this->model->actualizarEstadoDenuncia($this->getPost('denuncia'), constant('ESTADO_DENUNCIA_LEIDA'));
					$resultadoDenuncia = $this->model->leerDenuncia($this->getPost('denuncia'));
				}
				
				return $this->view->render($resultadoDenuncia, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function denunciaAceptar() {
		try{
			if($this->existPOST(['denuncia', 'mensaje'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('denuncia')) || caracteresInvalidos($this->getPost('mensaje'))) return new Errores(105);
				
				if(!$this->model->resolverDenuncia($this->getPost('denuncia'), $resultadoSesion['idUsuario'], $this->getPost('mensaje'), constant('ESTADO_DENUNCIA_ACEPTADA'))) {
					return new Errores(132);
				}
				
				$resultadoDenuncia = $this->model->leerDenuncia($this->getPost('denuncia'));
				
				// DENUNCIADO
				try{
					$email = new Email();

					$email->setEmail(strtolower($resultadoDenuncia['denunciadoEmail']));
					$parametros = ["{{alias}}", "{{mensaje}}", "{{respuesta}}", "{{numero}}"];
					$valores = [$resultadoDenuncia['denunciadoNombre'], $resultadoDenuncia['mensaje'], $this->getPost('mensaje'), $resultadoDenuncia['id']];
					$email->generarEmail("DENUNCIADO", $parametros, $valores, $resultadoDenuncia['id']);

					$email->envirEmail();
				}catch(PDOException $e){}
				
				// DENUNCIANTE
				try{
					$email = new Email();

					$email->setEmail(strtolower($resultadoDenuncia['denuncianteEmail']));
					$parametros = ["{{alias}}", "{{mensaje}}", "{{respuesta}}", "{{numero}}"];
					$valores = [$resultadoDenuncia['denuncianteNombre'], $resultadoDenuncia['mensaje'], $this->getPost('mensaje'), $resultadoDenuncia['id']];
					$email->generarEmail("DENUNCIANTE", $parametros, $valores, $resultadoDenuncia['id']);

					$email->envirEmail();
				}catch(PDOException $e){}
				
				return $this->view->render("", 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function denunciaRechazar() {
		try{
			if($this->existPOST(['denuncia', 'mensaje'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('denuncia')) || caracteresInvalidos($this->getPost('mensaje'))) return new Errores(105);
				
				if(!$this->model->resolverDenuncia($this->getPost('denuncia'), $resultadoSesion['idUsuario'], $this->getPost('mensaje'), constant('ESTADO_DENUNCIA_RECHAZADA'))) {
					return new Errores(132);
				}
				
				return $this->view->render('', 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function setTyc() {
		try{
			if($this->existPOST(['texto'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedeAdministrar'] != 1) return new Errores(124);
				
				if(!$this->model->setTyc($this->getPost('texto'))) {
					return new Errores(132);
				}
				
				return $this->view->render('', 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

}

?>
