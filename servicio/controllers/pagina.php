<?php
include_once 'libs/texto.php';
include_once 'libs/imagen.php';

if (!defined('NIVEL_SUSCRIPCION_GRATIS')) 	define('NIVEL_SUSCRIPCION_GRATIS', 0);
if (!defined('TIPO_SUSCRIPCION_GRATIS')) 	define('TIPO_SUSCRIPCION_GRATIS', 1);
if (!defined('NOMBRE_GRATIS')) 				define('NOMBRE_GRATIS', 'Gratis');
if (!defined('PAGINA_DESBLOQUEADA')) 		define('PAGINA_DESBLOQUEADA', 0);
if (!defined('CANTIDAD_ME_GUSTA_INICIAL'))	define('CANTIDAD_ME_GUSTA_INICIAL',			0);

class Pagina extends Controller{

    function __construct(){
        parent::__construct();
    }
	
	function crearPagina() {
		try{
			if($this->existPOST(['nombre', 'descripcion'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('nombre')) ||caracteresInvalidos($this->getPost('descripcion'))) {
					return new Errores(105);
				}
				
				if(lenghtInvalido($this->getPost('nombre'), 3) || lenghtInvalido($this->getPost('descripcion'), 3)){
					return new Errores(107);
				}
				
				if(strlen($this->getPost('nombre')) > constant('MAXIMO_CARACTERES_NOMBRE_CORTO')) return new Errores(133);
				if(strlen($this->getPost('descripcion')) > constant('MAXIMO_CARACTERES_TEXT')) return new Errores(133);
				
				$resultadoBuscarPagina = $this->model->getPaginaPorLiderNombre($resultadoSesion['idUsuario'],$this->getPost('nombre'));
				if ($this->checkError($resultadoBuscarPagina)) return;
				if (is_array($resultadoBuscarPagina)) {
					return new Errores(129);
				}
				
				if( $this->model->crearPagina( $resultadoSesion['idUsuario'], $this->getPost('nombre'), $this->getPost('descripcion'), constant('PAGINA_DESBLOQUEADA'), constant('TIPO_SUSCRIPCION_GRATIS'), constant('NIVEL_SUSCRIPCION_GRATIS'))){
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
	
	function listarPaginas() {
		try{
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				
				$resultadoBuscarPaginas = $this->model->listarPaginasPorUsuario($resultadoSesion['idUsuario']);
				
				if ($this->checkError($resultadoBuscarPaginas)) return;
				
				return $this->view->render($resultadoBuscarPaginas, 200);
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function eliminarPagina() {
		try{
			if($this->existPOST(['idPagina'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina'))) {
					return new Errores(105);
				}

				$resultadoEliminarPagina = $this->model->eliminarPaginaPorLiderId($resultadoSesion['idUsuario'],$this->getPost('idPagina'));
				if ($resultadoEliminarPagina) {
					borrarCarpeta(constant('UBICACION_IMAGENES_PUBLICACION') . $resultadoSesion['idUsuario'] . '/' . $this->getPost('idPagina'));
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
	
	function getPagina() {
		try{
			if($this->existPOST(['idPagina'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina'))) {
					return new Errores(105);
				}
				
				$resultadoGetPagina = $this->model->getPaginaPorLiderId($resultadoSesion['idUsuario'],$this->getPost('idPagina'));
				if ($resultadoGetPagina) {
					return $this->view->render($resultadoGetPagina, 200);
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
	
	function setNombrePagina() {
		try{
			if($this->existPOST(['idPagina','nombre'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('nombre'))) {
					return new Errores(105);
				}
				
				if(strlen($this->getPost('nombre')) > constant('MAXIMO_CARACTERES_NOMBRE_CORTO')) return new Errores(133);
				
				$resultadoSetNombrePagina = $this->model->setNombrePagina($this->getPost('idPagina'), $this->getPost('nombre'));
				if ($resultadoSetNombrePagina) {
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
	
	function setDescripcionPagina() {
		try{
			if($this->existPOST(['idPagina','descripcion'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('descripcion'))) {
					return new Errores(105);
				}
				
				if(strlen($this->getPost('descripcion')) > constant('MAXIMO_CARACTERES_TEXT')) return new Errores(133);
				
				$resultadoSetDescripcionPagina = $this->model->setDescripcionPagina($this->getPost('idPagina'), $this->getPost('descripcion'));
				if ($resultadoSetDescripcionPagina) {
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
	
	function setPagina() {
		try{
			if($this->existPOST(['idPagina','nombre','descripcion'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('nombre')) || caracteresInvalidos($this->getPost('descripcion'))) {
					return new Errores(105);
				}
				
				if(strlen($this->getPost('descripcion')) > constant('MAXIMO_CARACTERES_TEXT')) return new Errores(133);
				if(strlen($this->getPost('nombre')) > constant('MAXIMO_CARACTERES_NOMBRE_CORTO')) return new Errores(133);
				
				$resultadoSetDescripcionPagina = $this->model->setPagina($this->getPost('idPagina'), $this->getPost('nombre'), $this->getPost('descripcion'));
				if ($resultadoSetDescripcionPagina) {
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
	
	function setSuscripcionMinimaPagina() {
		try{
			if($this->existPOST(['idPagina','suscripcion'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('suscripcion'))) {
					return new Errores(105);
				}
				
				$resultadoGetSuscripcion = $this->model->getSuscripcionHabilitada($resultadoSesion['idUsuario'], $this->getPost('suscripcion'));
				if (! $resultadoGetSuscripcion) {
					return new Errores(131);
				}
				
				$resultadoSetDescripcionPagina = $this->model->setSuscripcionMinimaPagina($this->getPost('idPagina'), $this->getPost('suscripcion'), $resultadoGetSuscripcion['nivel']);
				if ($resultadoSetDescripcionPagina) {
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
	
	function crearPublicacion() {
		try{
			if($this->existPOST(['idPagina', 'mensaje', 'cantidadImagenes'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('mensaje')) || caracteresInvalidos($this->getPost('cantidadImagenes'))) {
					return new Errores(105);
				}
				
				if($this->getPost('cantidadImagenes') > 10 || $this->getPost('cantidadImagenes') < 0) {
					return new Errores(105);
				}
				
				if(lenghtInvalido($this->getPost('mensaje'), 1)){
					return new Errores(107);
				}
				if(strlen($this->getPost('mensaje')) > constant('MAXIMO_CARACTERES_TEXT')) return new Errores(133);
				
				$resultadoBuscarPagina = $this->model->getPaginaPorLiderId($resultadoSesion['idUsuario'],$this->getPost('idPagina'));
				if ($this->checkError($resultadoBuscarPagina)) return;
				if (!is_array($resultadoBuscarPagina)) {
					return new Errores(130);
				}
				
				$cantidadImagenes = 0;
				$nombreImagenes = "";
				
				$fecha = new DateTime();
				$urlCarpetaFinal = constant('UBICACION_IMAGENES_PUBLICACION') . $resultadoSesion['idUsuario'] . '/' . $this->getPost('idPagina');
				crearCarpeta($urlCarpetaFinal);
				for ($i = 1; $i <= $this->getPost('cantidadImagenes'); $i++) {
					
					$archivo = $_FILES['archivo' . $i]['name'];
					if(isset($archivo) && $archivo != ""){
						$tipo = $_FILES['archivo' . $i]['type'];
						$tamano = $_FILES['archivo' . $i]['size'];
						$temp = $_FILES['archivo' . $i]['tmp_name'];
						
						if ( !esFormatoValido($tipo) ) {
							return new Errores(143);
						} else if ( !esSizeValido($tamano) ){
							return new Errores(144);
						} else {
							$urlImagen = $fecha->format('Y-m-d') . encriptadoMd5Azar($resultadoSesion['nick']);
							$urlImagenFinal = $urlCarpetaFinal . '/' . $urlImagen . '.jpg';
							
							if (moverArchivo($temp, $urlImagenFinal)) {
								resizeImagenMax($urlImagenFinal, constant('MAXIMO_ANCHO_PUBLICACION'), constant('MAXIMO_ALTO_PUBLICACION'));
								$nombreImagenes .= ";" . $urlImagen;
								$cantidadImagenes++;
							} else {
								return new Errores(145);
							}
						}
					} else {
						return new Errores(104);
					}
					
				}
				
				if( $this->model->crearPublicacion( $this->getPost('idPagina'), $this->getPost('mensaje'), constant('CANTIDAD_ME_GUSTA_INICIAL'), $cantidadImagenes, $nombreImagenes)){
					if( $this->model->setUltimaActualizacionPagina( $this->getPost('idPagina')) ){
						return $this->view->render('', 200);
					} else {
						return new Errores(125);
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
	
	function listarPublicaciones() {
		try{
			if($this->existPOST(['pagina', 'idPagina'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('pagina')) || caracteresInvalidos($this->getPost('idPagina'))) {
					return new Errores(105);
				}
				
				$inicio = (((int) $this->getPost('pagina')) -1) * constant('ELEMENTOS_POR_PAGINA');
				
				$resultadoBuscarPagina = $this->model->getPaginaPorLiderId($resultadoSesion['idUsuario'],$this->getPost('idPagina'));
				if ($this->checkError($resultadoBuscarPagina)) return;
				if (!is_array($resultadoBuscarPagina)) {
					return new Errores(130);
				}
				
				$resultadoPublicaciones = $this->model->listarPublicaciones($resultadoSesion['idUsuario'], $inicio, constant('ELEMENTOS_POR_PAGINA'), $this->getPost('idPagina'));
				
				if ($this->checkError($resultadoPublicaciones)) return;
				
				return $this->view->render($resultadoPublicaciones, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function likePublicacion() {
		try{
			if($this->existPOST(['idPagina', 'idPublicacion'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('idPublicacion'))) {
					return new Errores(105);
				}
				
				$resultadoBuscarPagina = $this->model->getPaginaPorLiderId($resultadoSesion['idUsuario'],$this->getPost('idPagina'));
				if ($this->checkError($resultadoBuscarPagina)) return;
				if (!is_array($resultadoBuscarPagina)) {
					return new Errores(130);
				}
				
				$resultadoBuscarPublicacion = $this->model->getPublicacionPagina($this->getPost('idPublicacion'),$this->getPost('idPagina'));
				if (!$resultadoBuscarPublicacion) return new Errores(132);
				
				$resultadoPublicaciones = $this->model->likePublicacion($resultadoSesion['idUsuario'], $this->getPost('idPublicacion'));
				
				if (!$resultadoPublicaciones) return new Errores(125);
				
				return $this->view->render("", 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function dislikePublicacion() {
		try{
			if($this->existPOST(['idPagina', 'idPublicacion'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('idPublicacion'))) {
					return new Errores(105);
				}
				
				$resultadoBuscarPagina = $this->model->getPaginaPorLiderId($resultadoSesion['idUsuario'],$this->getPost('idPagina'));
				if ($this->checkError($resultadoBuscarPagina)) return;
				if (!is_array($resultadoBuscarPagina)) {
					return new Errores(130);
				}
				
				$resultadoBuscarPublicacion = $this->model->getPublicacionPagina($this->getPost('idPublicacion'),$this->getPost('idPagina'));
				if (!$resultadoBuscarPublicacion) return new Errores(132);
				
				$resultadoPublicaciones = $this->model->dislikePublicacion($resultadoSesion['idUsuario'], $this->getPost('idPublicacion'));
				
				if (!$resultadoPublicaciones) return new Errores(125);
				
				return $this->view->render("", 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function setPublicacion() {
		try{
			if($this->existPOST(['idPagina','idPublicacion', 'mensaje'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('idPublicacion')) || caracteresInvalidos($this->getPost('mensaje'))) {
					return new Errores(105);
				}
				
				if(lenghtInvalido($this->getPost('mensaje'), 1)){
					return new Errores(107);
				}
				if(strlen($this->getPost('mensaje')) > constant('MAXIMO_CARACTERES_TEXT')) return new Errores(133);
				
				$resultadoBuscarPagina = $this->model->getPaginaPorLiderId($resultadoSesion['idUsuario'],$this->getPost('idPagina'));
				if ($this->checkError($resultadoBuscarPagina)) return;
				if (!is_array($resultadoBuscarPagina)) {
					return new Errores(130);
				}
				
				if( $this->model->setPublicacion( $this->getPost('idPagina'), $this->getPost('idPublicacion'), $this->getPost('mensaje'))){
					if( $this->model->setUltimaActualizacionPagina( $this->getPost('idPagina')) ){
						return $this->view->render('', 200);
					} else {
						return new Errores(125);
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
	
	function getPublicacion() {
		try{
			if($this->existPOST(['idPublicacion', 'idPagina'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				$resultadoBuscarPagina = $this->model->getPaginaPorLiderId($resultadoSesion['idUsuario'],$this->getPost('idPagina'));
				if ($this->checkError($resultadoBuscarPagina)) return;
				if (!is_array($resultadoBuscarPagina)) {
					return new Errores(130);
				}
				
				$resultadoPublicaciones = $this->model->getPublicacion($resultadoSesion['idUsuario'], $this->getPost('idPublicacion'));
				
				if (!is_array($resultadoPublicaciones)) {
					return new Errores(130);
				}
				
				return $this->view->render($resultadoPublicaciones, 200);
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function setPublicacionAddImagen() {
		try{
			$archivo = $_FILES['archivo']['name'];
			if($this->existPOST(['idPublicacion']) && isset($archivo) && $archivo != ""){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPublicacion'))) {
					return new Errores(105);
				}
				
				$resultadoBuscarPublicacion = $this->model->getPublicacion($resultadoSesion['idUsuario'],$this->getPost('idPublicacion'));
				if(!is_array($resultadoBuscarPublicacion)) {
					return new Errores(132);
				}
				
				if($resultadoBuscarPublicacion['cantidadImagenes'] >= 10) {
					return new Errores(146);
				}		
				
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
					$urlCarpetaFinal = constant('UBICACION_IMAGENES_PUBLICACION') . $resultadoSesion['idUsuario'] . '/' . $resultadoBuscarPublicacion['pagina'];
					$urlImagenFinal = $urlCarpetaFinal . '/' . $urlImagen . '.jpg';
					
					crearCarpeta($urlCarpetaFinal);
					
					if (moverArchivo($temp, $urlImagenFinal)) {
						resizeImagenMax($urlImagenFinal, constant('MAXIMO_ANCHO_PUBLICACION'), constant('MAXIMO_ALTO_PUBLICACION'));
						if( $this->model->setPublicacionImagenes($this->getPost('idPublicacion'), $resultadoBuscarPublicacion['cantidadImagenes'] + 1, $resultadoBuscarPublicacion['nombreImagenes'] . ";" . $urlImagen) ) {
							return $this->view->render("", 200);
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
			return new Errores(999);
		}
	}
	
	function setPublicacionRemoveImagen() {
		try{
			if($this->existPOST(['idPublicacion', 'imagenNombre'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPublicacion')) || caracteresInvalidos($this->getPost('imagenNombre'))) {
					return new Errores(105);
				}
				
				$resultadoBuscarPublicacion = $this->model->getPublicacion($resultadoSesion['idUsuario'],$this->getPost('idPublicacion'));
				if(!is_array($resultadoBuscarPublicacion)) {
					return new Errores(132);
				}
				
				if($resultadoBuscarPublicacion['cantidadImagenes'] <= 0 || $resultadoBuscarPublicacion['nombreImagenes'] == "") {
					return new Errores(147);
				}
				
				// ENCONTRAR LA IMAGEN
				$ubicacion = -1;
				$arrayImagenes = explode(";", $resultadoBuscarPublicacion['nombreImagenes']);
				for ($i = 1; $i <= $resultadoBuscarPublicacion['cantidadImagenes']; $i++) {
					if( $arrayImagenes[$i] == $this->getPost('imagenNombre') ) {
						$ubicacion = $i;
					}
				}
				if($ubicacion == -1) {
					return new Errores(148);
				}
				
				// SACAR LA IMAGEN DE LA LISTA
				$stringImagenes = "";
				for ($i = 1; $i <= $resultadoBuscarPublicacion['cantidadImagenes']; $i++) {
					if( $i != $ubicacion ) {
						$stringImagenes .= ";" . $arrayImagenes[$i];
					}
				}
				
				if( $this->model->setPublicacionImagenes($this->getPost('idPublicacion'), $resultadoBuscarPublicacion['cantidadImagenes'] - 1, $stringImagenes) ) {
					borrarArchivo(constant('UBICACION_IMAGENES_PUBLICACION') . $resultadoSesion['idUsuario'] . '/' . $resultadoBuscarPublicacion['pagina'] . '/' . $this->getPost('imagenNombre') . '.jpg');
					return $this->view->render("", 200);
				} else {
					return new Errores(149);
				}
				
			} else {
				return new Errores(104);
			}
		}catch(PDOException $e){
			return new Errores(999);
		}
	}
	
	function eliminarPublicacion() {
		try{
			if($this->existPOST(['idPagina','idPublicacion'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina')) || caracteresInvalidos($this->getPost('idPublicacion'))) {
					return new Errores(105);
				}
				
				$resultadoBuscarPublicacion = $this->model->getPublicacion($resultadoSesion['idUsuario'],$this->getPost('idPublicacion'));
				if(!is_array($resultadoBuscarPublicacion)) {
					return new Errores(132);
				}
				
				if( $this->model->eliminarPublicacion( $this->getPost('idPagina'), $this->getPost('idPublicacion'))){
					$arrayImagenes = explode(";", $resultadoBuscarPublicacion['nombreImagenes']);
					for ($i = 1; $i <= $resultadoBuscarPublicacion['cantidadImagenes']; $i++) {
						borrarArchivo(constant('UBICACION_IMAGENES_PUBLICACION') . $resultadoSesion['idUsuario'] . '/' . $resultadoBuscarPublicacion['pagina'] . '/' . $arrayImagenes[$i] . '.jpg');
					}
					
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
	
	function getSeguidoresPagina() {
		try{
			if($this->existPOST(['idPagina'])){
				$resultadoSesion = $this->model->session->obtenerSession();
				if ($this->checkError($resultadoSesion)) return;
				if ($resultadoSesion['puedePublicar'] != 1) return new Errores(124);
				
				if(caracteresInvalidos($this->getPost('idPagina'))) {
					return new Errores(105);
				}
				
				$resultadoGetSeguidores = $this->model->getSeguidoresPagina($this->getPost('idPagina'));
				if ($resultadoGetSeguidores) {
					return $this->view->render($resultadoGetSeguidores, 200);
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

}

?>
