<?php
include_once 'libs/texto.php';

if (!defined('TIPO_SUSCRIPCION_GRATIS'))	define('TIPO_SUSCRIPCION_GRATIS',		1);
if (!defined('TIPO_SUSCRIPCION_OCULTA'))	define('TIPO_SUSCRIPCION_OCULTA',		3);

class Lider extends Controller{

    function __construct(){
        parent::__construct();
    }

    function getDatos(){
		try{
			$resultadoSesion = $this->model->session->obtenerSession();
			if ($this->checkError($resultadoSesion)) return;
			if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
			
			$resultadoBuscarLider = $this->model->getLider($resultadoSesion['idUsuario']);
			if ($this->checkError($resultadoBuscarLider)) return;
			
			return $this->view->render($resultadoBuscarLider, 200);
		}catch(PDOException $e){
			return new Errores(999);
		}
    }

    function listarTipoRelacionChats(){
		try{
			$resultadoSesion = $this->model->session->obtenerSession();
			if ($this->checkError($resultadoSesion)) return;
			if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
			
			$resultadoListarTipoRelacionChats = $this->model->listarTipoRelacionChats();
			if (!$resultadoListarTipoRelacionChats) return new Errores(132);
			
			return $this->view->render($resultadoListarTipoRelacionChats, 200);
		}catch(PDOException $e){
			return new Errores(999);
		}
    }

    function setTipoRelacionChats(){
		try{
			if($this->existPOST(['idTipoRelacionChats'])){
				if(caracteresInvalidos($this->getPost('idTipoRelacionChats'))) return new Errores(105);
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				$resultadoSetTipoRelacionChats = $this->model->setTipoRelacionChats($resultadoSesion['idUsuario'], $this->getPost('idTipoRelacionChats'));
				if (!$resultadoSetTipoRelacionChats) return new Errores(125);
				
				return $this->view->render('', 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
    }

    function setBiografia(){
		try{
			if($this->existPOST(['biografia'])){
				if(caracteresInvalidos($this->getPost('biografia'))) return new Errores(105);
				if(strlen($this->getPost('biografia')) > constant('MAXIMO_CARACTERES_TEXT')) return new Errores(133);
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				$resultadoBiografia = $this->model->setBiografia($resultadoSesion['idUsuario'], $this->getPost('biografia'));
				if (!$resultadoBiografia) return new Errores(125);
				
				return $this->view->render('', 200);
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
				
				$resultadoTipoSuscripcion = $this->model->listarTipoSuscripcion();
				
				if ($this->checkError($resultadoTipoSuscripcion)) return;
				
				return $this->view->render($resultadoTipoSuscripcion, 200);
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function listarTipoSuscripcionHabilitadas() {
		try{
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				$resultadoTipoSuscripcionHabilitadas = $this->model->listarTipoSuscripcionHabilitadas($resultadoSesion['idUsuario']);
				
				if ($this->checkError($resultadoTipoSuscripcionHabilitadas)) return;
				
				return $this->view->render($resultadoTipoSuscripcionHabilitadas, 200);
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

    function addTipoSuscripcionHabilitadas(){
		try{
			if($this->existPOST(['tipo', 'nombrePersonalizado'])){
				if(caracteresInvalidos($this->getPost('tipo')) || caracteresInvalidos($this->getPost('nombrePersonalizado'))) return new Errores(105);
				if(strlen($this->getPost('nombrePersonalizado')) > constant('MAXIMO_CARACTERES_NOMBRE_CORTO')) return new Errores(133);
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				$resultadoAddTipoRelacionChats = $this->model->addTipoSuscripcionHabilitadas($resultadoSesion['idUsuario'], $this->getPost('tipo'), $this->getPost('nombrePersonalizado'));
				if (!$resultadoAddTipoRelacionChats) return new Errores(125);
				
				return $this->view->render('', 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
    }

    function borrarTipoSuscripcionHabilitadas(){
		try{
			if($this->existPOST(['tipo'])){
				if(caracteresInvalidos($this->getPost('tipo'))) return new Errores(105);
				if($this->getPost('tipo') == constant('TIPO_SUSCRIPCION_GRATIS')) return new Errores(134);
				if($this->getPost('tipo') == constant('TIPO_SUSCRIPCION_OCULTA')) return new Errores(134);
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				$resultadoBuscarPaginaTipoSuscripcion = $this->model->listarPaginasLiderTipoSuscripcion($resultadoSesion['idUsuario'],$this->getPost('tipo'));
				if ($this->checkError($resultadoBuscarPaginaTipoSuscripcion)) return;
				if (is_array($resultadoBuscarPaginaTipoSuscripcion) && count($resultadoBuscarPaginaTipoSuscripcion) > 0) {
					return new Errores(135);
				}
				
				$resultadoSetTipoRelacionChats = $this->model->borrarTipoSuscripcionHabilitadas($resultadoSesion['idUsuario'], $this->getPost('tipo'));
				if (!$resultadoSetTipoRelacionChats) return new Errores(125);
				
				return $this->view->render('', 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
    }

    function modificarTipoSuscripcionHabilitadas(){
		try{
			if($this->existPOST(['tipo', 'nombrePersonalizado'])){
				if(caracteresInvalidos($this->getPost('tipo')) || caracteresInvalidos($this->getPost('nombrePersonalizado'))) return new Errores(105);
				if(strlen($this->getPost('nombrePersonalizado')) > constant('MAXIMO_CARACTERES_NOMBRE_CORTO')) return new Errores(133);
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				$resultadoAddTipoRelacionChats = $this->model->modificarTipoSuscripcionHabilitadas($resultadoSesion['idUsuario'], $this->getPost('tipo'), $this->getPost('nombrePersonalizado'));
				if (!$resultadoAddTipoRelacionChats) return new Errores(125);
				
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
