<?php
include_once 'libs/texto.php';

if (!defined('TIPO_PAGINA_GRATIS')) 	define('TIPO_PAGINA_GRATIS', 0);
if (!defined('TIPO_PAGINA_OCULTA')) 	define('TIPO_PAGINA_OCULTA', 99);
if (!defined('USUARIO_INEXISTENTE')) 	define('USUARIO_INEXISTENTE', -1);

class Seguir extends Controller{

    function __construct(){
        parent::__construct();
    }
	
	function seguirPagina() {
		try{
			if($this->existPOST(['idLider', 'idPagina'])){
				$nivelMinimo = constant('TIPO_PAGINA_GRATIS');
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				if(caracteresInvalidos($this->getPost('idLider')) || caracteresInvalidos($this->getPost('idPagina'))) {
					return new Errores(105);
				}
				
				if( $this->getPost('idLider') == $resultadoSesion['idUsuario'] ) {
					$nivelMinimo = constant('TIPO_PAGINA_OCULTA');
				} else {
					$resultadoSuscripcion = $this->model->getSuscripcion($resultadoSesion['idUsuario'], $this->getPost('idLider'));
					if ($this->checkError($resultadoSuscripcion)) return;
					
					if (is_array($resultadoSuscripcion)) {
						$nivelMinimo = $resultadoSuscripcion['suscripcionNivel'];
					}
				}
				
				$resultadoPagina = $this->model->getPagina($this->getPost('idLider'), $this->getPost('idPagina'));
				if ($this->checkError($resultadoPagina)) return;
				
				if (is_array($resultadoPagina)) {
					if( $resultadoPagina['nivelMinimo'] > $nivelMinimo ) {
						return new Errores(136);
					} else {
						if( $this->model->seguirPagina($resultadoSesion['idUsuario'], $this->getPost('idLider'), $this->getPost('idPagina')) ) {
							return $this->view->render('', 200);
						} else {
							return new Errores(125);
						}
					}
				} else {
					return new Errores(130);
				}
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function dejarSeguirPagina() {
		try{
			if($this->existPOST(['idLider', 'idPagina'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				if(caracteresInvalidos($this->getPost('idLider')) || caracteresInvalidos($this->getPost('idPagina'))) {
					return new Errores(105);
				}

				if( $this->model->dejarSeguirPagina($resultadoSesion['idUsuario'], $this->getPost('idLider'), $this->getPost('idPagina')) ) {
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
	
	function listarSiguiendo() {
		try{
			$resultadoSesion = $this->model->session->obtenerSession();
			if ($this->checkError($resultadoSesion)) return;

			$resultadoListarSiguiendo = $this->model->listarSiguiendo($resultadoSesion['idUsuario']);
			if (is_array($resultadoListarSiguiendo)) {
				return $this->view->render($resultadoListarSiguiendo, 200);
			} else {
				return new Errores(131);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function likePublicacion() {
		try{
			if($this->existPOST(['idLider', 'idPagina', 'idPublicacion'])){
				$nivelMinimo = constant('TIPO_PAGINA_GRATIS');
				
				if(caracteresInvalidos($this->getPost('idLider')) || caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('idPublicacion'))) {
					return new Errores(105);
				}
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($resultadoSesion) {
					if( $this->getPost('idLider') == $resultadoSesion['idUsuario'] ) {
						$nivelMinimo = constant('TIPO_PAGINA_OCULTA');
					} else {
						$resultadoSuscripcion = $this->model->getSuscripcion($resultadoSesion['idUsuario'], $this->getPost('idLider'));
						if ($this->checkError($resultadoSuscripcion)) return;
						
						if (is_array($resultadoSuscripcion)) {
							$nivelMinimo = $resultadoSuscripcion['suscripcionNivel'];
						}
					}
				}
				
				if($nivelMinimo != constant('TIPO_PAGINA_OCULTA')) {
					$resultadoBuscarPagina = $this->model->getPaginaNivel($this->getPost('idPagina'), $this->getPost('idLider'), $nivelMinimo);
					if (!is_array($resultadoBuscarPagina)) return new Errores(137);
				}
				
				$resultadoBuscarPublicacion = $this->model->getPublicacionPagina($this->getPost('idPublicacion'), $this->getPost('idPagina'));
				if (!is_array($resultadoBuscarPublicacion)) return new Errores(137);
				
				if( $this->model->likePublicacion($resultadoSesion['idUsuario'], $this->getPost('idPublicacion')) ) {
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

	function dislikePublicacion() {
		try{
			if($this->existPOST(['idLider', 'idPagina', 'idPublicacion'])){
				$nivelMinimo = constant('TIPO_PAGINA_GRATIS');
				
				if(caracteresInvalidos($this->getPost('idLider')) || caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('idPublicacion'))) {
					return new Errores(105);
				}
				
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($resultadoSesion) {
					if( $this->getPost('idLider') == $resultadoSesion['idUsuario'] ) {
						$nivelMinimo = constant('TIPO_PAGINA_OCULTA');
					} else {
						$resultadoSuscripcion = $this->model->getSuscripcion($resultadoSesion['idUsuario'], $this->getPost('idLider'));
						if ($this->checkError($resultadoSuscripcion)) return;
						
						if (is_array($resultadoSuscripcion)) {
							$nivelMinimo = $resultadoSuscripcion['suscripcionNivel'];
						}
					}
				}
				
				if($nivelMinimo != constant('TIPO_PAGINA_OCULTA')) {
					$resultadoBuscarPagina = $this->model->getPaginaNivel($this->getPost('idPagina'), $this->getPost('idLider'), $nivelMinimo);
					if (!is_array($resultadoBuscarPagina)) return new Errores(137);
				}
				
				$resultadoBuscarPublicacion = $this->model->getPublicacionPagina($this->getPost('idPublicacion'), $this->getPost('idPagina'));
				if (!is_array($resultadoBuscarPublicacion)) return new Errores(137);
				
				if( $this->model->dislikePublicacion($resultadoSesion['idUsuario'], $this->getPost('idPublicacion')) ) {
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
	
	function muroNovedades() {
		try{
			$resultadoSesion = $this->model->session->obtenerSession();
			if ($this->checkError($resultadoSesion)) return;
			
			$fechaMaxima = '';
			if($this->existPOST(['fechaMaxima'])) {
				if(caracteresInvalidos($this->getPost('fechaMaxima'))) {
					return new Errores(105);
				}
				$fechaMaxima = $this->getPost('fechaMaxima');
			} else {
				$fecha = new DateTime();
				$fechaMaxima = $fecha->format('Y-m-d H:i:s');
			}

			$resultadoMuroNovedades = $this->model->muroNovedades($resultadoSesion['idUsuario'], $fechaMaxima, constant('ELEMENTOS_POR_PAGINA'));
			if (is_array($resultadoMuroNovedades)) {
				return $this->view->render($resultadoMuroNovedades, 200);
			} else {
				return new Errores(132);
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
				
				$resultadoLider = $this->model->getLider($this->getPost('idDenunciado'));
				if(!$resultadoLider) return new Errores(125);
				
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
	
	////////////////////////////////////// FUNCIONES CON Y SIN LOGUEO //////////////////////////////////////

	function listarLideres() {
		try{
			if($this->existPOST(['pagina'])){
				$inicio = (((int) $this->getPost('pagina')) -1) * constant('ELEMENTOS_POR_PAGINA');
				$filtros = "";
				
				if($this->existPOST(['busqueda']) && $this->getPost('busqueda') != '') {
					if(caracteresInvalidos($this->getPost('busqueda'))) return new Errores(105);
					$filtros .= " AND ";
					$filtros .= " (u.nombreSimplificado like '%" . simplificar($this->getPost('busqueda')) . "%' OR";
					$filtros .= "  u.nickSimplificado   like '%" . simplificar($this->getPost('busqueda')) . "%')";
				}
				
				$resultado = $this->model->listarLideres($inicio,constant('ELEMENTOS_POR_PAGINA'),$filtros);
					
				return $this->view->render($resultado, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function listarPaginasLider() {
		try{
			if($this->existPOST(['idLider'])){
				$nivelMinimo = constant('TIPO_PAGINA_GRATIS');
				
				if(caracteresInvalidos($this->getPost('idLider'))) {
					return new Errores(105);
				}
				
				$resultadoSesion = $this->model->session->obtenerSessionSinErrores();
				if ($resultadoSesion) {
					if( $this->getPost('idLider') == $resultadoSesion['idUsuario'] ) {
						$nivelMinimo = constant('TIPO_PAGINA_OCULTA');
					} else {
						$resultadoSuscripcion = $this->model->getSuscripcion($resultadoSesion['idUsuario'], $this->getPost('idLider'));
						if ($this->checkError($resultadoSuscripcion)) return;
						
						if (is_array($resultadoSuscripcion)) {
							$nivelMinimo = $resultadoSuscripcion['suscripcionNivel'];
						}
					}
				}
				
				if($nivelMinimo == constant('TIPO_PAGINA_OCULTA')) {
					$resultadoBuscarPaginas = $this->model->getPaginasLiderPropias($this->getPost('idLider'));
				} else {
					$resultadoBuscarPaginas = $this->model->getPaginasLider($this->getPost('idLider'), $nivelMinimo);
				}
				if ($this->checkError($resultadoBuscarPaginas)) return;
				
				$resultadoDetalleLider = $this->model->detalleLider($resultadoSesion['idUsuario'], $this->getPost('idLider'));
				$resultadoDetalleLider['paginas'] = $resultadoBuscarPaginas;
				
				return $this->view->render($resultadoDetalleLider, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function detalleLider() {
		try{
			if($this->existPOST(['idLider'])){
				$idUsuario = constant('USUARIO_INEXISTENTE');
				
				if(caracteresInvalidos($this->getPost('idLider'))) {
					return new Errores(105);
				}
				
				$resultadoSesion = $this->model->session->obtenerSessionSinErrores();
				if ($resultadoSesion) {
					$idUsuario = $resultadoSesion['idUsuario'];
				}
				
				$resultadoDetalleLider = $this->model->detalleLider($idUsuario, $this->getPost('idLider'));
				if ($this->checkError($resultadoSesion)) return;
				
				if (is_array($resultadoDetalleLider)) {
					return $this->view->render($resultadoDetalleLider, 200);
				} else {
					return new Errores(131);
				}
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function listarTipoSuscripcionHabilitadas() {
		try{
			if($this->existPOST(['idLider'])){
				
				if(caracteresInvalidos($this->getPost('idLider'))) {
					return new Errores(105);
				}
				
				$resultadoListarSuscripcion = $this->model->listarTipoSuscripcionHabilitadas($this->getPost('idLider'));
				if ($this->checkError($resultadoSesion)) return;
				
				if (is_array($resultadoListarSuscripcion)) {
					return $this->view->render($resultadoListarSuscripcion, 200);
				} else {
					return new Errores(131);
				}
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

	function listarPublicacionesPagina() {
		try{
			if($this->existPOST(['idLider', 'idPagina'])){
				$nivelMinimo = constant('TIPO_PAGINA_GRATIS');
				$idUsuario = constant('USUARIO_INEXISTENTE');
				$resultadoBuscarPagina = null;
				
				if(caracteresInvalidos($this->getPost('idLider')) || caracteresInvalidos($this->getPost('idLider'))) {
					return new Errores(105);
				}
				
				$resultadoSesion = $this->model->session->obtenerSessionSinErrores();
				if ($resultadoSesion) {
					$idUsuario = $resultadoSesion['idUsuario'];
					if( $this->getPost('idLider') == $resultadoSesion['idUsuario'] ) {
						$nivelMinimo = constant('TIPO_PAGINA_OCULTA');
					} else {
						$resultadoSuscripcion = $this->model->getSuscripcion($resultadoSesion['idUsuario'], $this->getPost('idLider'));
						if ($this->checkError($resultadoSuscripcion)) return;
						
						if (is_array($resultadoSuscripcion)) {
							$nivelMinimo = $resultadoSuscripcion['suscripcionNivel'];
						}
					}
				}
				
				$resultadoBuscarPagina = $this->model->getPaginaNivel($this->getPost('idPagina'), $this->getPost('idLider'), $nivelMinimo);
				if (!is_array($resultadoBuscarPagina)) return new Errores(137);
				
				$resultadoBuscarPublicaciones = [];
				
				if ($resultadoSesion) {
					$resultadoUsuarioSiguePagina = $this->model->usuarioSiguePagina($resultadoSesion['idUsuario'], $this->getPost('idLider'), $this->getPost('idPagina'));
					
					if (is_array($resultadoUsuarioSiguePagina)) {
						$resultadoBuscarPagina['siguiendo'] = 1;
						$this->model->actualizarSiguiendo($resultadoSesion['idUsuario'], $this->getPost('idLider'), $this->getPost('idPagina'));
					} else {
						$resultadoBuscarPagina['siguiendo'] = 0;
					}
				}

				$resultadoLider = $this->model->getLider($this->getPost('idLider'));
				if(!$resultadoLider) return new Errores(125);
				
				if( $resultadoBuscarPagina['bloqueada'] == 0 ) {
					if($this->existPOST(['idPrimero', 'idUltimo'])) {
						$resultadoBuscarPublicaciones = $this->model->getPaginaPublicacionesPrimeroUltimo($idUsuario, $this->getPost('idPagina'), constant('ELEMENTOS_POR_PAGINA'), $this->getPost('idPrimero'), $this->getPost('idUltimo'));
					} else {
						$resultadoBuscarPublicaciones = $this->model->getPaginaPublicaciones($idUsuario, $this->getPost('idPagina'), constant('ELEMENTOS_POR_PAGINA'));
					}
				}
				
				for ($i = 0; $i < sizeof($resultadoBuscarPublicaciones); $i++) {
					$resultadoBuscarPublicaciones[$i]['lider'] = $resultadoLider['id'];
					$resultadoBuscarPublicaciones[$i]['nombre'] = $resultadoLider['nombre'];
					$resultadoBuscarPublicaciones[$i]['nombrePagina'] = $resultadoBuscarPagina['nombre'];
					$resultadoBuscarPublicaciones[$i]['nick'] = $resultadoLider['nick'];
					$resultadoBuscarPublicaciones[$i]['urtImagen'] = $resultadoLider['urtImagen'];
				}
				
				$resultadoBuscarPagina['publicaciones'] = $resultadoBuscarPublicaciones;
				
				return $this->view->render($resultadoBuscarPagina, 200);
				
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function muroPublico() {
		try{
			$idUsuadio = -1;
			$resultadoSesion = $this->model->session->obtenerSessionSinErrores();
			if ($resultadoSesion) {
				$idUsuadio = $resultadoSesion['idUsuario'];
			}
			
			$fechaMaxima = '';
			if($this->existPOST(['fechaMaxima'])) {
				if(caracteresInvalidos($this->getPost('fechaMaxima'))) {
					return new Errores(105);
				}
				$fechaMaxima = $this->getPost('fechaMaxima');
			} else {
				$fecha = new DateTime();
				$fechaMaxima = $fecha->format('Y-m-d H:i:s');
			}

			$resultadoMuroNovedades = $this->model->muroPublico($fechaMaxima, constant('ELEMENTOS_POR_PAGINA'), $idUsuadio);
			if (is_array($resultadoMuroNovedades)) {
				return $this->view->render($resultadoMuroNovedades, 200);
			} else {
				return new Errores(132);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}

}

?>