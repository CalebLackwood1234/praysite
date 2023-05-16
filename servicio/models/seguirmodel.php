<?php

if (!defined('TIPO_PAGINA_OCULTA')) 	define('TIPO_PAGINA_OCULTA', 99);
if (!defined('USUARIO_TIPO_LIDER')) 	define('USUARIO_TIPO_LIDER', 2);
if (!defined('ESTADO_DENUNCIA_INICIADA'))	define('ESTADO_DENUNCIA_INICIADA', 1);

class SeguirModel extends Model {

	public function __construct(){
		parent::__construct();
	}
	
	public function listarLideres($inicio, $elementosPorPagina, $filtros){
		$items = [];

		try{
			$query = $this->prepare("SELECT u.id, u.nick, u.nombre, u.urtImagen, MAX(p.ultimaActualizacion) AS ultimaActualizacionPagina from usuario AS u INNER JOIN pagina AS p ON u.tipoUsuario = 2 " . $filtros . " AND u.id = p.lider AND p.bloqueada = 0 AND p.nivelMinimo = 0 GROUP BY u.id ORDER BY ultimaActualizacionPagina DESC LIMIT :inicio, :elementosPorPagina;");
			$query->execute([
				'inicio' => $inicio,
				'elementosPorPagina' => $elementosPorPagina]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getSuscripcion($idUsuario, $idLider){
		try{
			$query = $this->prepare('SELECT * FROM suscripcion WHERE usuario = :idUsuario AND lider = :idLider AND problemaDePago = 0');
			$query->execute([	'idUsuario' => $idUsuario,
								'idLider' => $idLider
							]);
			$suscripcion = $query->fetch(PDO::FETCH_ASSOC);
			return $suscripcion;
		}catch(PDOException $e){
			return $this->returnError(131);
		}
	}
	
	public function getPagina($idLider, $idPagina){
		try{
			$query = $this->prepare('SELECT * FROM pagina WHERE id = :idPagina AND lider = :idLider AND bloqueada = 0');
			$query->execute([	'idLider' => $idLider,
								'idPagina' => $idPagina
							]);
			$pagina = $query->fetch(PDO::FETCH_ASSOC);
			return $pagina;
		}catch(PDOException $e){
			return $this->returnError(131);
		}
	}
	
	public function listarPagina($idUsuario, $idLider, $idPagina){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('INSERT INTO siguiendo (usuario, lider, pagina, ultimoAcceso) VALUES (:idUsuario, :idLider, :idPagina, :ultimaActualizacion)');
			$query->execute([
				'idUsuario' => $idUsuario,
				'idLider' => $idLider,
				'idPagina' => $idPagina,
				'ultimaActualizacion' => $fecha->format('Y-m-d H:i:s')
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function seguirPagina($idUsuario, $idLider, $idPagina){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('INSERT INTO siguiendo (usuario, lider, pagina, ultimoAcceso) VALUES (:idUsuario, :idLider, :idPagina, :ultimoAcceso)');
			$query->execute([
				'idUsuario' => $idUsuario,
				'idLider' => $idLider,
				'idPagina' => $idPagina,
				'ultimoAcceso' => $fecha->format('Y-m-d H:i:s')
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function dejarSeguirPagina($idUsuario, $idLider, $idPagina){
		try{
			$query = $this->prepare('DELETE FROM siguiendo WHERE usuario = :idUsuario AND pagina = :idPagina AND lider = :idLider');
			$query->execute([	'idUsuario' => $idUsuario,
								'idPagina' => $idPagina,
								'idLider' => $idLider
							]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function listarSiguiendo($idUsuario){
		$items = [];

		try{
			//$query = $this->prepare('SELECT p.*, s.ultimoAcceso, p.ultimaActualizacion > s.ultimoAcceso AS novedades FROM siguiendo AS s INNER JOIN pagina AS p ON s.usuario = :idUsuario AND s.pagina = p.id ORDER BY novedades DESC, p.ultimaActualizacion DESC');
			$query = $this->prepare('SELECT p.*, s.ultimoAcceso, p.ultimaActualizacion > s.ultimoAcceso AS novedades, u.nombre AS liderNombre, u.urtImagen AS LiderUrlImagen, u.nick AS liderNick FROM siguiendo AS s INNER JOIN pagina AS p ON s.usuario = :idUsuario AND s.pagina = p.id INNER JOIN usuario AS u ON u.id = s.lider ORDER BY novedades DESC, p.ultimaActualizacion DESC');
			$query->execute([ 'idUsuario' => $idUsuario ]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function actualizarSiguiendo($idUsuario, $idLider, $idPagina){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE siguiendo SET ultimoAcceso = :ultimoAcceso WHERE usuario = :idUsuario AND pagina = :idPagina AND lider = :idLider');
			$query->execute([
				'idUsuario' => $idUsuario,
				'idLider' => $idLider,
				'idPagina' => $idPagina,
				'ultimoAcceso' => $fecha->format('Y-m-d H:i:s')
			]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function usuarioSiguePagina($idUsuario, $idLider, $idPagina){
		try{
			$query = $this->prepare('SELECT * FROM siguiendo WHERE usuario = :idUsuario AND pagina = :idPagina AND lider = :idLider');
			$query->execute([
				'idUsuario' => $idUsuario,
				'idLider' => $idLider,
				'idPagina' => $idPagina
			]);
			$pagina = $query->fetch(PDO::FETCH_ASSOC);
			return $pagina;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getPaginaPublicaciones($idUsuario, $idPagina, $elementosPorPagina){
		$items = [];

		try{
			$query = $this->prepare('SELECT p.*, m.usuario IS NOT NULL AS dioMegusta FROM publicacion AS p LEFT JOIN megusta AS m ON p.id = m.publicacion AND m.usuario = :idUsuario WHERE p.pagina = :idPagina ORDER BY p.id DESC LIMIT 0, :elementosPorPagina');
			$query->execute([ 'idPagina' => $idPagina,
							  'elementosPorPagina' => $elementosPorPagina,
							  'idUsuario' => $idUsuario ]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getPaginaPublicacionesPrimeroUltimo($idUsuario, $idPagina, $elementosPorPagina, $idPrimero, $idUltimo){
		$items = [];

		try{
			$query = $this->prepare('SELECT p.*, m.usuario IS NOT NULL AS dioMegusta FROM publicacion AS p LEFT JOIN megusta AS m ON p.id = m.publicacion AND m.usuario = :idUsuario WHERE p.pagina = :idPagina AND (p.id < :idPrimero OR p.id > :idUltimo) ORDER BY p.id DESC LIMIT 0, :elementosPorPagina');
			$query->execute([ 'idPagina' => $idPagina,
							  'elementosPorPagina' => $elementosPorPagina,
							  'idPrimero' => $idPrimero,
							  'idUltimo' => $idUltimo,
							  'idUsuario' => $idUsuario ]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getPublicacionPagina($idPublicacion, $idPagina){
		try{
			$query = $this->prepare('SELECT * FROM publicacion WHERE id = :idPublicacion AND pagina = :idPagina');
			$query->execute([	'idPublicacion' => $idPublicacion,
								'idPagina' => $idPagina
							]);
			$pagina = $query->fetch(PDO::FETCH_ASSOC);
			return $pagina;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function likePublicacion($idUsuario, $idPublicacion){
		$fecha = new DateTime();
		
		try{
			$consulta = $this->procedure('CALL sp_agregar_megusta(:publicacion_in, :usuario_in, :fecha_in, @error_out)',
				['publicacion_in' => $idPublicacion,
				'usuario_in' => $idUsuario,
				'fecha_in' => $fecha->format('Y-m-d H:i:s')],
				'SELECT @error_out AS error;');
			
			if($consulta['error'] != 0) {
				return false;
			}

			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function dislikePublicacion($idUsuario, $idPublicacion){
		try{
			$consulta = $this->procedure('CALL sp_quitar_megusta(:usuario_in, :publicacion_in, @error_out)',
				['publicacion_in' => $idPublicacion,
				'usuario_in' => $idUsuario],
				'SELECT @error_out AS error;');
			
			if($consulta['error'] != 0) {
				return false;
			}

			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getPaginaNivel($idPagina, $idLider, $nivel){
		try{
			$query = $this->prepare('SELECT * FROM pagina WHERE id = :idPagina AND lider = :idLider AND nivelMinimo <= :nivel');
			$query->execute([	'idPagina' => $idPagina,
								'idLider' => $idLider,
								'nivel' => $nivel
							]);
			$pagina = $query->fetch(PDO::FETCH_ASSOC);
			return $pagina;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getLider($idLider){
		try{
			$query = $this->prepare('SELECT * FROM usuario WHERE id = :idLider AND tipoUsuario = :tipoLider');
			$query->execute([	'idLider' => $idLider,
								'tipoLider' => constant('USUARIO_TIPO_LIDER')
							]);
			$lider = $query->fetch(PDO::FETCH_ASSOC);
			return $lider;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getPaginasLider($idLider, $nivel){
		$items = [];

		try{
			$query = $this->prepare('SELECT *, nivelMinimo <= :nivel AS habilitada FROM pagina WHERE lider = :idLider AND nivelMinimo < 99');
			$query->execute([	'idLider' => $idLider,
								'nivel' => $nivel
							]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return $this->returnError(999);
		}
	}
	
	public function getPaginasLiderPropias($idLider){
		$items = [];

		try{
			$query = $this->prepare('SELECT *, 1 AS habilitada FROM pagina WHERE lider = :idLider');
			$query->execute([ 'idLider' => $idLider ]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return $this->returnError(999);
		}
	}

	public function detalleLider($idUsuario, $idLider){
		try{
			$query = $this->prepare('SELECT l.biografia, l.habilitado, l.tipoRelacionChats, u.nombre AS liderNombre, u.nick AS liderNick, u.urtImagen AS liderUrlImagen, s.fechaProximoPago, s.fechaUltimoPago, s.suscripcion, s.suscripcionNivel, s.problemaDePago FROM lider AS l INNER JOIN usuario AS u ON l.id = :idLider AND l.id = u.id LEFT JOIN suscripcion AS s ON l.id = s.lider AND s.usuario = :idUsuario');
			$query->execute([	'idLider' => $idLider,
								'idUsuario' => $idUsuario
							]);
			$lider = $query->fetch(PDO::FETCH_ASSOC);
			return $lider;
		}catch(PDOException $e){
			return $this->returnError(131);
		}
	}

	public function listarTipoSuscripcionHabilitadas($idLider){
		$items = [];

		try{
			$query = $this->prepare('SELECT t.*, h.nombrePersonalizado FROM suscripcionhabilitada AS h INNER JOIN tiposuscripcion AS t ON h.lider = :idLider AND h.tipo = t.id WHERE t.nivel < ' . constant('TIPO_PAGINA_OCULTA'));
			$query->execute(['idLider' => $idLider]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return $this->returnError(999);
		}
	}
	
	public function muroNovedades($idUsuario, $fechaMaxima, $elementosPorPagina){
		$items = [];

		try{
			$query = $this->prepare('SELECT pa.id AS idPagina, pa.lider AS idLider, pa.nombre AS nombrePagina, u.urtImagen AS imagenLider, pu.id AS idPublicacion, pu.fechaAlta, pu.fechaModificacion, pu.mensaje, pu.meGusta, me.publicacion IS NOT NULL AS dioMegusta FROM pagina AS pa INNER JOIN publicacion AS pu ON pa.id IN (SELECT s.pagina FROM siguiendo AS s INNER JOIN pagina AS p ON s.usuario = :idUsuario AND s.pagina = p.id AND p.bloqueada = 0 LEFT JOIN suscripcion AS su ON s.lider = su.lider AND su.problemaDePago = 0 WHERE p.nivelMinimo = 0 OR (su.suscripcionNivel IS NOT NULL AND su.suscripcionNivel >= p.nivelMinimo)) AND pa.id = pu.pagina AND pu.fechaModificacion < :fechaMaxima INNER JOIN usuario AS u ON pa.lider = u.id LEFT JOIN megusta AS me ON me.usuario = :idUsuario2 AND me.publicacion = pu.id ORDER BY pu.fechaModificacion DESC LIMIT 0, ' . $elementosPorPagina);
			$query->execute([ 'idUsuario' => $idUsuario,
							  'idUsuario2' => $idUsuario,
							  'fechaMaxima' => $fechaMaxima
							]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function generarDenuncia($idDenunciante, $idDenunciado, $mensaje){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('INSERT INTO denuncia (denunciante, denunciado, fechaAlta, mensaje, estado) VALUES (:idDenunciante, :idDenunciado, :fechaAlta, :mensaje, :estado)');
			$query->execute([
				'idDenunciante' => $idDenunciante,
				'idDenunciado' => $idDenunciado,
				'fechaAlta' => $fecha->format('Y-m-d H:i:s'),
				'mensaje' => $mensaje,
				'estado' => constant('ESTADO_DENUNCIA_INICIADA')
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function muroPublico($fechaMaxima, $elementosPorPagina, $idUsuario){
		$items = [];

		try{
			$query = $this->prepare('SELECT pa.id AS idPagina, pa.lider AS idLider, pa.nombre AS nombrePagina, u.urtImagen AS imagenLider, u.nick AS nickLider, u.nombre AS nombreLider, pu.id AS idPublicacion, pu.fechaAlta, pu.fechaModificacion, pu.mensaje, pu.meGusta, pu.nombreImagenes, me.publicacion IS NOT NULL AS dioMegusta FROM pagina AS pa INNER JOIN publicacion AS pu ON pa.nivelMinimo = 0 AND pa.bloqueada = 0 AND pa.id = pu.pagina AND pu.fechaModificacion < :fechaMaxima INNER JOIN usuario AS u ON pa.lider = u.id LEFT JOIN megusta AS me ON me.usuario = :idUsuario2 AND me.publicacion = pu.id ORDER BY pu.fechaModificacion DESC LIMIT 0, ' . $elementosPorPagina);
			$query->execute([ 
							  'idUsuario2' => $idUsuario,
							  'fechaMaxima' => $fechaMaxima
							]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}

}
?>