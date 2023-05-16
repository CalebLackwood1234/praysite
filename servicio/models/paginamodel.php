<?php

if (!defined('CODIGO_DESBLOQUEADO'))				define('CODIGO_DESBLOQUEADO', 				0);

class PaginaModel extends Model {

	public function __construct(){
		parent::__construct();
	}
	
	public function getPaginaPorLiderNombre($lider, $nombre){
		try{
			$query = $this->prepare('SELECT * FROM pagina WHERE lider = :lider AND nombre = :nombre');
			$query->execute([	'lider' => $lider,
								'nombre' => $nombre]);
			$pagina = $query->fetch(PDO::FETCH_ASSOC);
			return $pagina;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getPublicacionPagina($idPublicacion, $idPagina){
		try{
			$query = $this->prepare('SELECT * FROM publicacion WHERE id = :idPublicacion AND pagina = :idPagina');
			$query->execute([	'idPagina' => $idPagina,
								'idPublicacion' => $idPublicacion]);
			$pagina = $query->fetch(PDO::FETCH_ASSOC);
			return $pagina;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function eliminarPaginaPorLiderId($lider, $id){
		try{
			$query = $this->prepare('DELETE FROM pagina WHERE lider = :lider AND id = :id ');
			$query->execute([	'lider' => $lider,
								'id' => $id]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function crearPagina($lider, $nombre, $descripcion, $bloqueada, $suscripcionMinimo, $nivelMinimo){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('INSERT INTO pagina (lider, nombre, descripcion, bloqueada, suscripcionMinimo, nivelMinimo, ultimaActualizacion) VALUES (:lider, :nombre, :descripcion, :bloqueada, :suscripcionMinimo, :nivelMinimo, :ultimaActualizacion)');
			$query->execute([
				'lider' => $lider,
				'nombre' => $nombre,
				'descripcion' => $descripcion,
				'bloqueada' => $bloqueada,
				'suscripcionMinimo' => $suscripcionMinimo,
				'nivelMinimo' => $nivelMinimo,
				'ultimaActualizacion' => $fecha->format('Y-m-d H:i:s')
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function listarPaginasPorUsuario($lider){
		$items = [];

		try{
			$query = $this->prepare('SELECT p.*, s.nombrePersonalizado AS nombrePersonalizaSuscripcionMinimo FROM pagina AS p INNER JOIN suscripcionhabilitada AS s ON p.lider = :lider AND p.suscripcionMinimo = s.tipo AND s.lider = p.lider');
			$query->execute([	'lider' => $lider]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return $this->returnError(999);
		}
	}
	
	public function getPaginaPorLiderId($lider, $id){
		try{
			$query = $this->prepare('SELECT p.*, s.nombrePersonalizado AS nombrePersonalizaSuscripcionMinimo FROM pagina AS p INNER JOIN suscripcionhabilitada AS s ON p.lider = :lider AND p.id = :id AND p.suscripcionMinimo = s.tipo AND s.lider = p.lider');
			$query->execute([	'lider' => $lider,
								'id' => $id]);
			$pagina = $query->fetch(PDO::FETCH_ASSOC);
			return $pagina;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getSeguidoresPagina($pagina){
		try{
			$query = $this->prepare('SELECT u.nick, u.nombre, u.tipoUsuario, u.estaBloqueado, u.fechaUltimaAccion, s.ultimoAcceso FROM siguiendo AS s INNER JOIN usuario AS u ON s.pagina = :pagina AND u.id = s.usuario');
			$query->execute([
								'pagina' => $pagina
							]);
			$seguidores = $query->fetch(PDO::FETCH_ASSOC);
			return $seguidores;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setNombrePagina($idPagina, $nombre){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE pagina SET nombre = :nombre, ultimaActualizacion = :ultimaActualizacion WHERE id = :idPagina');
			$query->execute([	'idPagina' => $idPagina,
								'nombre' => $nombre,
								'ultimaActualizacion' => $fecha->format('Y-m-d H:i:s')]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setDescripcionPagina($idPagina, $descripcion){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE pagina SET descripcion = :descripcion, ultimaActualizacion = :ultimaActualizacion WHERE id = :idPagina');
			$query->execute([	'idPagina' => $idPagina,
								'descripcion' => $descripcion,
								'ultimaActualizacion' => $fecha->format('Y-m-d H:i:s')]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setPagina($idPagina, $nombre, $descripcion){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE pagina SET nombre = :nombre, descripcion = :descripcion, ultimaActualizacion = :ultimaActualizacion WHERE id = :idPagina');
			$query->execute([	'idPagina' => $idPagina,
								'nombre' => $nombre,
								'descripcion' => $descripcion,
								'ultimaActualizacion' => $fecha->format('Y-m-d H:i:s')]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getSuscripcionHabilitada($idLider, $idSuscipcion){
		try{
			$query = $this->prepare('SELECT t.* FROM tipoSuscripcion AS t INNER JOIN suscripcionhabilitada AS h ON h.tipo = :idSuscipcion AND h.lider = :idLider AND t.id = h.tipo');
			$query->execute([	'idLider' => $idLider,
								'idSuscipcion' => $idSuscipcion
							]);
			$pagina = $query->fetch(PDO::FETCH_ASSOC);
			return $pagina;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setSuscripcionMinimaPagina($idPagina, $suscripcionMinimo, $nivelMinimo){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE pagina SET suscripcionMinimo = :suscripcionMinimo, nivelMinimo = :nivelMinimo, ultimaActualizacion = :ultimaActualizacion WHERE id = :idPagina');
			$query->execute([	'idPagina' => $idPagina,
								'suscripcionMinimo' => $suscripcionMinimo,
								'nivelMinimo' => $nivelMinimo,
								'ultimaActualizacion' => $fecha->format('Y-m-d H:i:s')]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setUltimaActualizacionPagina($idPagina){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE pagina SET ultimaActualizacion = :ultimaActualizacion WHERE id = :idPagina');
			$query->execute([	'idPagina' => $idPagina,
								'ultimaActualizacion' => $fecha->format('Y-m-d H:i:s')]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function crearPublicacion($idPagina, $mensaje, $meGusta, $cantidadImagenes, $nombreImagenes){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('INSERT INTO publicacion (pagina, mensaje, meGusta, fechaAlta, fechaModificacion, cantidadImagenes, nombreImagenes) VALUES (:pagina, :mensaje, :meGusta, :fechaAlta, :fechaModificacion, :cantidadImagenes, :nombreImagenes)');
			$query->execute([
				'pagina' => $idPagina,
				'mensaje' => $mensaje,
				'meGusta' => $meGusta,
				'fechaAlta' => $fecha->format('Y-m-d H:i:s'),
				'fechaModificacion' => $fecha->format('Y-m-d H:i:s'),
				'cantidadImagenes' => $cantidadImagenes,
				'nombreImagenes' => $nombreImagenes
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function listarPublicaciones($idUsuario, $inicio, $elementosPorPagina, $idPagina){
		$items = [];

		try{
			$query = $this->prepare('SELECT p.*, m.usuario IS NOT NULL AS dioMegusta FROM publicacion AS p LEFT JOIN megusta AS m ON p.id = m.publicacion AND m.usuario = :idUsuario WHERE p.pagina = :idPagina ORDER BY p.id DESC LIMIT :inicio, :elementosPorPagina');
			$query->execute([
				'idUsuario' => $idUsuario,
				'inicio' => $inicio,
				'elementosPorPagina' => $elementosPorPagina,
				'idPagina' => $idPagina
				]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return $this->returnError(999);
		}
	}
	
	public function likePublicacion($idUsuario, $idPublicacion){
		try{
			$query = $this->prepare('INSERT INTO megusta (usuario, publicacion) VALUES (:idUsuario, :idPublicacion);');
			$query->execute([
				'idUsuario' => $idUsuario,
				'idPublicacion' => $idPublicacion
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function dislikePublicacion($idUsuario, $idPublicacion){
		try{
			$query = $this->prepare('DELETE FROM megusta WHERE usuario = :idUsuario AND publicacion = :idPublicacion;');
			$query->execute([
				'idUsuario' => $idUsuario,
				'idPublicacion' => $idPublicacion
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setPublicacion($idPagina, $idPublicacion, $mensaje){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE publicacion SET mensaje = :mensaje, fechaModificacion = :fechaModificacion WHERE pagina = :idPagina AND id = :idPublicacion');
			$query->execute([	'idPagina' => $idPagina,
								'idPublicacion' => $idPublicacion,
								'mensaje' => $mensaje,
								'fechaModificacion' => $fecha->format('Y-m-d H:i:s')
								]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getPublicacion($idUsuario, $idPublicacion){
		try{
			$query = $this->prepare('SELECT pu.* FROM publicacion AS pu INNER JOIN pagina AS pa ON pu.id = :idPublicacion AND pu.pagina = pa.id AND pa.lider = :idUsuario');
			$query->execute([
				'idUsuario' => $idUsuario,
				'idPublicacion' => $idPublicacion
			]);
			$publicacion = $query->fetch(PDO::FETCH_ASSOC);
			return $publicacion;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setPublicacionImagenes($idPublicacion, $cantidadImagenes, $nombreImagenes){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE publicacion SET fechaModificacion = :fechaModificacion, cantidadImagenes = :cantidadImagenes, nombreImagenes = :nombreImagenes WHERE id = :idPublicacion');
			$query->execute([	
								'idPublicacion' => $idPublicacion,
								'cantidadImagenes' => $cantidadImagenes,
								'nombreImagenes' => $nombreImagenes,
								'fechaModificacion' => $fecha->format('Y-m-d H:i:s')
								]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function eliminarPublicacion($idPagina, $idPublicacion){
		try{
			$query = $this->prepare('DELETE FROM publicacion WHERE pagina = :idPagina AND id = :idPublicacion');
			$query->execute([	'idPagina' => $idPagina,
								'idPublicacion' => $idPublicacion
							]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	

}
?>