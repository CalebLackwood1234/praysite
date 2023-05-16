<?php
include_once 'libs/texto.php';

if (!defined('ESTADO_CHAT_NORMAL'))				define('ESTADO_CHAT_NORMAL', 1);
if (!defined('ESTADO_CHAT_BLOQUEADO'))			define('ESTADO_CHAT_BLOQUEADO', 2);
if (!defined('TIENE_NOVEDAD'))					define('TIENE_NOVEDAD', 1);
if (!defined('NO_TIENE_NOVEDAD'))				define('NO_TIENE_NOVEDAD', 0);

class Mensaje extends Controller{

    function __construct(){
        parent::__construct();
    }
	
	function permitirChat( $idEmisor, $idReceptor) {
		$resultadoBuscarRelacionChats = $this->model->relacionDeChats($idEmisor, $idReceptor);
		if (!$resultadoBuscarRelacionChats) return $this->model->returnError(132);
		
		// Chequea si yo tengo deshabilitados los chats
		for($i = 0; $i < count($resultadoBuscarRelacionChats); ++$i) {
			if( $resultadoBuscarRelacionChats[$i]['id'] == $idEmisor && $resultadoBuscarRelacionChats[$i]['deshabilitado'] == "1" ) {
				return $this->model->returnError(140);
			}
		}
		// Chequea si el otro usuario tiene deshabilitados los chats
		for($i = 0; $i < count($resultadoBuscarRelacionChats); ++$i) {
			if( $resultadoBuscarRelacionChats[$i]['deshabilitado'] == "1" ) {
				return $this->model->returnError(139);
			}
		}
		
		// Chequea si algunos de los usuarios bloqueo la conversacion
		$chatIniciado = false;
		if(((int) $idEmisor) < ((int) $idReceptor)) {
			$resultadoBuscarChat = $this->model->getChat($idEmisor, $idReceptor);
			if( $resultadoBuscarChat ) {
				$chatIniciado = true;
				if( $resultadoBuscarChat['estadoChatIdMenor'] != constant('ESTADO_CHAT_NORMAL') ) {
					return $this->model->returnError(141);
				}
				if( $resultadoBuscarChat['estadoChatIdMayor'] != constant('ESTADO_CHAT_NORMAL') ) {
					return $this->model->returnError(139);
				}
			}
		} else {
			$resultadoBuscarChat = $this->model->getChat($idReceptor, $idEmisor);
			if( $resultadoBuscarChat ) {
				$chatIniciado = true;
				if( $resultadoBuscarChat['estadoChatIdMayor'] != constant('ESTADO_CHAT_NORMAL') ) {
					return $this->model->returnError(141);
				}
				if( $resultadoBuscarChat['estadoChatIdMenor'] != constant('ESTADO_CHAT_NORMAL') ) {
					return $this->model->returnError(139);
				}
			}
		}
		
		// Chequea si alguno de los lideres involucrados tiene conversaciones libres (Y ya lo doy por bueno)
		for($i = 0; $i < count($resultadoBuscarRelacionChats); ++$i) {
			if( $resultadoBuscarRelacionChats[$i]['soloCreados'] == "0" && $resultadoBuscarRelacionChats[$i]['suscripcionMinimaNivel'] == "0" ) {
				return true;
			}
		}
		
		// Chequea si alguno de los lideres involucrados tiene conversaciones creadas (Y ya lo doy por bueno)
		for($i = 0; $i < count($resultadoBuscarRelacionChats); ++$i) {
			if( $resultadoBuscarRelacionChats[$i]['soloCreados'] == "1" && $chatIniciado ) {
				return true;
			}
		}
		
		// Chequea si se cumple algunas de las suscripciones (Y ya lo doy por bueno)
		for($i = 0; $i < count($resultadoBuscarRelacionChats); ++$i) {
			if( $resultadoBuscarRelacionChats[$i]['suscripcionMinimaNivel'] != "0" ) {
				if( $resultadoBuscarRelacionChats[$i]['id'] == $idEmisor ) {
					$resultadoBuscarSuscripcion = $this->model->getSuscripcion($idReceptor, $idEmisor);
					if($resultadoBuscarSuscripcion && $resultadoBuscarSuscripcion['suscripcionNivel'] >= $resultadoBuscarRelacionChats[$i]['suscripcionMinimaNivel']) {
						return true;
					}
				} else {
					$resultadoBuscarSuscripcion = $this->model->getSuscripcion($idEmisor, $idReceptor);
					if($resultadoBuscarSuscripcion && $resultadoBuscarSuscripcion['suscripcionNivel'] >= $resultadoBuscarRelacionChats[$i]['suscripcionMinimaNivel']) {
						return true;
					}
				}
			}
		}
		
		// Chequea si alguno de los lideres involucrados tiene conversaciones creadas (Y ya lo doy por bueno)
		for($i = 0; $i < count($resultadoBuscarRelacionChats); ++$i) {
			if( $resultadoBuscarRelacionChats[$i]['id'] == $idEmisor ) {
				return $this->model->returnError(142);
			}
		}
		
		return $this->model->returnError(139);
	}
	
	function obtenerChat( $idEmisor, $idReceptor) {
		
		if(((int) $idEmisor) < ((int) $idReceptor)) {
			$resultadoBuscarChat = $this->model->getChat($idEmisor, $idReceptor);
			if( $resultadoBuscarChat ) {
				return $resultadoBuscarChat;
			}
			
			if( !$this->model->addChat($idEmisor, $idReceptor) ) {
				return $this->model->returnError(125);
			}
			
			$resultadoBuscarChat = $this->model->getChat($idEmisor, $idReceptor);
			if( $resultadoBuscarChat ) {
				return $resultadoBuscarChat;
			}
			
			return $this->model->returnError(132);
		} else {
			$resultadoBuscarChat = $this->model->getChat($idReceptor, $idEmisor);
			if( $resultadoBuscarChat ) {
				return $resultadoBuscarChat;
			}
			
			if( !$this->model->addChat($idReceptor, $idEmisor) ) {
				return $this->model->returnError(125);
			}
			
			$resultadoBuscarChat = $this->model->getChat($idReceptor, $idEmisor);
			if( $resultadoBuscarChat ) {
				return $resultadoBuscarChat;
			}
			
			return $this->model->returnError(132);
		}
	}
	
	function escribir() {
		try{
			if($this->existPOST(['idReceptor', 'mensaje'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				if(caracteresInvalidos($this->getPost('idReceptor')) || caracteresInvalidos($this->getPost('mensaje'))) {
					return new Errores(105);
				}
				
				if( $this->getPost('idReceptor') == $resultadoSesion['idUsuario'] ) {
					return new Errores(138);
				}
				
				$resultadoPermitir = $this->permitirChat($resultadoSesion['idUsuario'], $this->getPost('idReceptor'));
				if ($this->checkError($resultadoPermitir)) return;
				
				$resultadoConsultaChat = $this->obtenerChat($resultadoSesion['idUsuario'], $this->getPost('idReceptor'));
				if ($this->checkError($resultadoConsultaChat)) return;
				
				if( !$this->model->addMensaje($resultadoConsultaChat['id'], $resultadoSesion['idUsuario'], $this->getPost('mensaje')) ) {
					return new Errores(125);
				}
				
				$mensajePreview = "";
				if (strlen($this->getPost('mensaje')) > constant('MAXIMO_CARACTERES_PREVIEW_MENSAJE')) {
					$mensajePreview = substr($this->getPost('mensaje'), 0, constant('MAXIMO_CARACTERES_PREVIEW_MENSAJE')) . '...';
				} else {
					$mensajePreview = $this->getPost('mensaje');
				}
   
				if( !$this->model->updateChat($resultadoConsultaChat['id'], ((int) $resultadoConsultaChat['cantidadMensajes']) + 1, $mensajePreview, $resultadoSesion['idUsuario']) ) {
					return new Errores(125);
				}
   
				$this->model->updateMensajeUsuario($this->getPost('idReceptor'), constant('TIENE_NOVEDAD'));
				$this->model->updateMensajeUsuario($resultadoSesion['idUsuario'], constant('TIENE_NOVEDAD'));
				
				return $this->view->render('', 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function listarChats() {
		try{
			$resultadoSesion = $this->model->session->obtenerSession();
			if ($this->checkError($resultadoSesion)) return;
			
			$fechaMinima = '';
			if($this->existPOST(['fechaMinima'])){
				if(caracteresInvalidos($this->getPost('fechaMinima'))) {
					return new Errores(105);
				}
				$fechaMinima = $this->getPost('fechaMinima');
			} else {
				$fecha = new DateTime();
				$fechaMinima = $fecha->format('Y-m-d H:i:s');
			}
			
			$resultadoChats = $this->model->getUltimosChats($resultadoSesion['idUsuario'], constant('ELEMENTOS_POR_PAGINA'), $fechaMinima);
			if (!is_array($resultadoChats)) return new Errores(132);
   
			$this->model->updateMensajeUsuario($resultadoSesion['idUsuario'], constant('NO_TIENE_NOVEDAD'));
			
			return $this->view->render($resultadoChats, 200);
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function listarChatsTodos() {
		try{
			$resultadoSesion = $this->model->session->obtenerSession();
			if ($this->checkError($resultadoSesion)) return;
			
			$resultadoChats = $this->model->getChatsTodos($resultadoSesion['idUsuario']);
			if (!is_array($resultadoChats)) return new Errores(132);
   
			$this->model->updateMensajeUsuario($resultadoSesion['idUsuario'], constant('NO_TIENE_NOVEDAD'));
			
			return $this->view->render($resultadoChats, 200);
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function leerChat() {
		try{
			if($this->existPOST(['idChat'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				if(caracteresInvalidos($this->getPost('idChat'))) {
					return new Errores(105);
				}
				
				$resultadoChat = $this->model->getChatIdUsuario($this->getPost('idChat'), $resultadoSesion['idUsuario']);
				if (!$resultadoChat) return new Errores(131);
				
				$idMensajeMinimo = -1;
				$idMensajeMaximo = -1;
				if($this->existPOST(['idMensajeMinimo', 'idMensajeMaximo'])){
					if(caracteresInvalidos($this->getPost('idMensajeMinimo')) || caracteresInvalidos($this->getPost('idMensajeMaximo'))) {
						return new Errores(105);
					}
					$idMensajeMinimo = $this->getPost('idMensajeMinimo');
					$idMensajeMaximo = $this->getPost('idMensajeMaximo');
				}
				
				$idUsuarioOtro = 0;
				if( $resultadoChat['usuarioIdMenor'] == $resultadoSesion['idUsuario'] ) {
					$idUsuarioOtro = $resultadoChat['usuarioIdMayor'];
				} else {
					$idUsuarioOtro = $resultadoChat['usuarioIdMenor'];
				}
				
				$resultadoMensajes = $this->model->getUltimosMensajes($this->getPost('idChat'), $idUsuarioOtro, $idMensajeMinimo, $idMensajeMaximo, constant('ELEMENTOS_POR_PAGINA'));
				if (!is_array($resultadoMensajes)) return new Errores(131);
				
				if( $resultadoChat['previewUltimoMensajeUsuarioId'] != $resultadoSesion['idUsuario'] ) {
					$this->model->updateVistoChat($this->getPost('idChat'));
					$this->model->updateMensajeUsuario($resultadoSesion['idUsuario'], constant('TIENE_NOVEDAD'));
				}
				
				return $this->view->render($resultadoMensajes, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function leerChatNuevos() {
		try{
			if($this->existPOST(['idChat', 'idMensajeMaximo'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				
				if(caracteresInvalidos($this->getPost('idChat'))) {
					return new Errores(105);
				}
				
				$resultadoChat = $this->model->getChatIdUsuario($this->getPost('idChat'), $resultadoSesion['idUsuario']);
				if (!$resultadoChat) return new Errores(131);
				
				$idMensajeMaximo = $this->getPost('idMensajeMaximo');
				
				$idUsuarioOtro = 0;
				if( $resultadoChat['usuarioIdMenor'] == $resultadoSesion['idUsuario'] ) {
					$idUsuarioOtro = $resultadoChat['usuarioIdMayor'];
				} else {
					$idUsuarioOtro = $resultadoChat['usuarioIdMenor'];
				}
				
				$resultadoMensajes = $this->model->getUltimosMensajesNuevos($this->getPost('idChat'), $idUsuarioOtro, $idMensajeMaximo);
				if (!is_array($resultadoMensajes)) return new Errores(131);
				
				if( $resultadoChat['previewUltimoMensajeUsuarioId'] != $resultadoSesion['idUsuario'] ) {
					$this->model->updateVistoChat($this->getPost('idChat'));
					$this->model->updateMensajeUsuario($resultadoSesion['idUsuario'], constant('TIENE_NOVEDAD'));
				}
				
				return $this->view->render($resultadoMensajes, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function bloquearChat() {
		try{
			if($this->existPOST(['idChat'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				if(caracteresInvalidos($this->getPost('idChat'))) {
					return new Errores(105);
				}
				
				$resultadoChat = $this->model->getChatIdUsuario($this->getPost('idChat'), $resultadoSesion['idUsuario']);
				if (!$resultadoChat) return new Errores(131);
				
				if( $resultadoChat['usuarioIdMenor'] == $resultadoSesion['idUsuario'] ) {
					$resultadoChat = $this->model->updateChatBloqueoIdMenor($this->getPost('idChat'), constant('ESTADO_CHAT_BLOQUEADO'));
				} else {
					$resultadoChat = $this->model->updateChatBloqueoIdMayor($this->getPost('idChat'), constant('ESTADO_CHAT_BLOQUEADO'));
				}
				
				return $this->view->render('', 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function desbloquearChat() {
		try{
			if($this->existPOST(['idChat'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				if(caracteresInvalidos($this->getPost('idChat'))) {
					return new Errores(105);
				}
				
				$resultadoChat = $this->model->getChatIdUsuario($this->getPost('idChat'), $resultadoSesion['idUsuario']);
				if (!$resultadoChat) return new Errores(131);
				
				if( $resultadoChat['usuarioIdMenor'] == $resultadoSesion['idUsuario'] ) {
					$resultadoChat = $this->model->updateChatBloqueoIdMenor($this->getPost('idChat'), constant('ESTADO_CHAT_NORMAL'));
				} else {
					$resultadoChat = $this->model->updateChatBloqueoIdMayor($this->getPost('idChat'), constant('ESTADO_CHAT_NORMAL'));
				}
				
				return $this->view->render('', 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function denunciar() {
		try{
			if($this->existPOST(['idDenunciado', 'mensaje'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				if(caracteresInvalidos($this->getPost('idDenunciado')) || caracteresInvalidos($this->getPost('mensaje'))) {
					return new Errores(105);
				}
				
				if( $this->getPost('idDenunciado') == $resultadoSesion['idUsuario'] ) {
					return new Errores(138);
				}
				
				$resultadoPermitir = $this->permitirChat($resultadoSesion['idUsuario'], $this->getPost('idDenunciado'));
				if ($this->checkError($resultadoPermitir)) return;
				
				$resultadoDenuncia = $this->model->generarDenuncia($resultadoSesion['idUsuario'], $this->getPost('idDenunciado'), $this->getPost('mensaje'));
				if (!$resultadoDenuncia) return new Errores(125);
				
				return $this->view->render('', 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function buscarChat() {
		try{
			if($this->existPOST(['lider'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				if(caracteresInvalidos($this->getPost('lider'))) {
					return new Errores(105);
				}
				
				$resultadoBuscarChat = $this->model->buscarChat($this->getPost('lider'), $resultadoSesion['idUsuario']);
				if ($this->checkError($resultadoBuscarChat)) return;
				
				return $this->view->render($resultadoBuscarChat, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function puedeEscribir() {
		try{
			if($this->existPOST(['idReceptor'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				$resultadoConsultaChat = $this->permitirChat($resultadoSesion['idUsuario'], $this->getPost('idReceptor'));
				if ($this->checkError($resultadoConsultaChat)) return;
				
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