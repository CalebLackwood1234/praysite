<?php

if (!defined('TIPO_USUARIO_ADMINISTRADOR')) define('TIPO_USUARIO_ADMINISTRADOR',	1);
if (!defined('INTENTOS_PASSWORD')) 			define('INTENTOS_PASSWORD',				10);

class AdministradorModel extends Model {

	public function __construct(){
		parent::__construct();
	}
	
	public function listarUsuarios($inicio, $elementosPorPagina, $filtros){
		$items = [];

		try{
			$query = $this->prepare('SELECT u.email, u.estaBloqueado, u.estaVerificado, u.fechaAlta, u.fechaNacimiento, u.fechaUltimaAccion, u.fechaUltimoLogueo, u.id, u.nick, u.nombre, u.tipoUsuario, u.urtImagen, t.nombre AS tipoUsuarioDescripcion FROM usuario AS u INNER JOIN tipoUsuario t ON u.tipoUsuario = t.id ' . $filtros . ' ORDER BY u.nick ASC LIMIT :inicio, :elementosPorPagina;');
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
	
	public function setTipoUsuario($id, $tipoUsuario){
		try{
			$query = $this->prepare('UPDATE usuario SET tipoUsuario = :tipoUsuario WHERE id = :id AND tipoUsuario != ' . constant('TIPO_USUARIO_ADMINISTRADOR'));
			$query->execute([	'id' => $id,
								'tipoUsuario' => $tipoUsuario]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setEstaBloqueado($id, $estaBloqueado){
		try{
			$query = $this->prepare('UPDATE usuario SET estaBloqueado = :estaBloqueado WHERE id = :id AND tipoUsuario != ' . constant('TIPO_USUARIO_ADMINISTRADOR'));
			$query->execute([	'id' => $id,
								'estaBloqueado' => $estaBloqueado]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setEmail($id, $email){
		try{
			$query = $this->prepare('UPDATE usuario SET email = :email, emailPropuesto = NULL WHERE id = :id AND tipoUsuario != ' . constant('TIPO_USUARIO_ADMINISTRADOR'));
			$query->execute([	'id' => $id,
								'email' => $email]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setUsuarioVerificarEmail($id){
		try{
			$query = $this->prepare('UPDATE usuario SET intentos = ' . constant('INTENTOS_PASSWORD') . ', estaVerificado = 1, codigoVerificacion = null, tipoCodigoVerificacion = null WHERE id = :id AND tipoUsuario != ' . constant('TIPO_USUARIO_ADMINISTRADOR'));
			$query->execute(['id' => $id]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getUsuarioEmail($email){
		try{
			$query = $this->prepare('SELECT * FROM usuario WHERE email = :email');
			$query->execute(['email' => $email]);
			$user = $query->fetch(PDO::FETCH_ASSOC);
			return $user;
		}catch(PDOException $e){
			return $this->returnError(110);
		}
	}

	public function setUsuarioEmailPropuesto($id, $emailPropuesto){
		try{
			$query = $this->prepare('UPDATE usuario SET emailPropuesto = :emailPropuesto WHERE id = :id');
			$query->execute([	'id' => $id,
								'emailPropuesto' => $emailPropuesto]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}

	public function setPaginasBloqueo($idLider, $estado){
		try{
			$query = $this->prepare('UPDATE pagina SET bloqueada = :estado WHERE lider = :lider');
			$query->execute([	'lider' => $idLider,
								'estado' => $estado]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}

	public function setPaginaIdBloqueo($idLider, $idPagina, $estado){
		try{
			$query = $this->prepare('UPDATE pagina SET bloqueada = :estado WHERE lider = :lider and id = :id');
			$query->execute([	'id' => $idPagina,
								'lider' => $idLider,
								'estado' => $estado]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getUsuarioId($id){
		try{
			$query = $this->prepare('SELECT email, estaBloqueado, estaVerificado, fechaAlta, fechaNacimiento, fechaUltimaAccion, fechaUltimoLogueo, id, nick, nombre, tipoUsuario, urtImagen FROM usuario WHERE id = :id');
			$query->execute(['id' => $id]);
			$user = $query->fetch(PDO::FETCH_ASSOC);
			return $user;
		}catch(PDOException $e){
			return $this->returnError(110);
		}
	}
	
	public function getUsuarioLiderId($id){
		try{
			$query = $this->prepare('SELECT u.*, l.biografia, l.habilitado, l.tipoRelacionChats FROM usuario AS u LEFT JOIN lider AS l ON u.id = l.id WHERE u.id = :id');
			$query->execute(['id' => $id]);
			$user = $query->fetch(PDO::FETCH_ASSOC);
			return $user;
		}catch(PDOException $e){
			return $this->returnError(110);
		}
	}
	
	public function getSuscripcionHabilitada($lider, $tipoSuscripcion){
		try{
			$query = $this->prepare('SELECT * FROM suscripcionHabilitada WHERE lider = :lider AND tipo = :tipoSuscripcion');
			$query->execute([	'lider' => $lider,
								'tipoSuscripcion' => $tipoSuscripcion]);
			$suscripcion = $query->fetch(PDO::FETCH_ASSOC);
			return $suscripcion;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function addSuscripcionHabilitada($lider, $tipo, $nombrePersonalizado){
		try{
			$query = $this->prepare('INSERT INTO suscripcionhabilitada (lider, tipo, nombrePersonalizado) VALUES(:lider, :tipo, :nombrePersonalizado)');
			$query->execute([
				'lider' => $lider,
				'tipo' => $tipo,
				'nombrePersonalizado' => $nombrePersonalizado
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}

	public function setLiderHabilitar($id, $habilitado){
		try{
			$query = $this->prepare('UPDATE lider SET habilitado = :habilitado WHERE id = :id');
			$query->execute([	'id' => $id,
								'habilitado' => $habilitado]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function addLider($id, $biografia, $habilitado, $tipoRelacionChats){
		try{
			$query = $this->prepare('INSERT INTO lider (id, biografia, habilitado, tipoRelacionChats) VALUES(:id, :biografia, :habilitado, :tipoRelacionChats)');
			$query->execute([
				'id' => $id,
				'biografia' => $biografia,
				'habilitado' => $habilitado,
				'tipoRelacionChats' => $tipoRelacionChats
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setUsuarioCodigoVerificacion($id, $codigoVerificacion, $tipoCodigoVerificacion){
		try{
			$query = $this->prepare('UPDATE usuario SET codigoVerificacion = :codigoVerificacion, tipoCodigoVerificacion = :tipoCodigoVerificacion WHERE id = :id');
			$query->execute([	'id' => $id,
								'codigoVerificacion' => $codigoVerificacion,
								'tipoCodigoVerificacion' => $tipoCodigoVerificacion]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setUsuarioPassword($id, $semillaPass, $semillaSesion, $hashPass){
		try{
			$query = $this->prepare('UPDATE usuario SET intentos = ' . constant('INTENTOS_PASSWORD') . ', codigoVerificacion = null, tipoCodigoVerificacion = null, hashPass = :hashPass, semillaPass = :semillaPass, semillaSesion = :semillaSesion WHERE id = :id');
			$query->execute([	'id' => $id,
								'hashPass' => $hashPass,
								'semillaPass' => $semillaPass,
								'semillaSesion' => $semillaSesion]);
			$query = $this->prepare('DELETE FROM sesioniniciada WHERE usuario = :usuario');
			$query->execute(['usuario' => $id]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function listarPaginasPorLider($lider){
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
	
	public function setBloqueoPagina($idPagina, $bloqueo){
		try{
			$query = $this->prepare('UPDATE pagina SET bloqueada = :bloqueo WHERE id = :idPagina');
			$query->execute([	'idPagina' => $idPagina,
								'bloqueo' => $bloqueo]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getPagina($id){
		try{
			$query = $this->prepare('SELECT p.*, u.email AS email, u.nombre AS nombreUsuario FROM pagina AS p INNER JOIN usuario AS u ON p.id = :id AND p.lider = u.id');
			$query->execute([	
								'id' => $id
							]);
			$pagina = $query->fetch(PDO::FETCH_ASSOC);
			return $pagina;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function listarTipoSuscripcion(){
		$items = [];

		try{
			$query = $this->prepare('SELECT * FROM tipoSuscripcion ORDER BY nivel');
			$query->execute();

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return $this->returnError(999);
		}
	}
	
	public function addTipoSuscripcion($nivel, $nombre, $precio, $descripcion){
		try{
			$query = $this->prepare('INSERT INTO tiposuscripcion (nivel, nombre, precio, descripcion) VALUES(:nivel, :nombre, :precio, :descripcion)');
			$query->execute([
				'nivel' => $nivel,
				'nombre' => $nombre,
				'precio' => $precio,
				'descripcion' => $descripcion
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function modificarTipoSuscripcion($idSuscripcion, $nombre, $precio, $descripcion){
		try{
			$query = $this->prepare('UPDATE tiposuscripcion SET nombre = :nombre, precio = :precio, descripcion = :descripcion WHERE id = :idSuscripcion');
			$query->execute([	'nombre' => $nombre,
								'precio' => $precio,
								'descripcion' => $descripcion,
								'idSuscripcion' => $idSuscripcion]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function eliminarTipoSuscripcion($idSuscripcion){
		try{
			$query = $this->prepare('DELETE FROM tiposuscripcion WHERE id = :idSuscripcion');
			$query->execute(['idSuscripcion' => $idSuscripcion]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function listarDenuncias($inicio, $elementosPorPagina, $filtros){
		$items = [];

		try{
			$query = $this->prepare('SELECT d.*, u1.nombre AS denuncianteNombre, u1.nick AS denuncianteNick, u1.email AS denuncianteEmail, u1.urtImagen AS denuncianteUrlImagen, u2.nombre AS denunciadoNombre, u2.nick AS denunciadoNick, u2.email AS denunciadoEmail, u2.urtImagen AS denunciadoUrlImagen, ed.nombre AS estadoNombre, ed.leida AS denunciaLeida, ed.aceptada AS denunciaAceptada, ed.rechazada AS denunciaRechazada FROM denuncia AS d INNER JOIN usuario AS u1 ON d.denunciante = u1.id INNER JOIN usuario u2 ON d.denunciado = u2.id INNER JOIN estadodenuncia AS ed ON ed.id = d.estado ' . $filtros . ' ORDER BY d.id DESC LIMIT :inicio, :elementosPorPagina;');
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
	
	public function leerDenuncia($denuncia){
		try{
			$query = $this->prepare('SELECT d.*, u1.nombre AS denuncianteNombre, u1.nick AS denuncianteNick, u1.email AS denuncianteEmail, u1.urtImagen AS denuncianteUrlImagen, u2.nombre AS denunciadoNombre, u2.nick AS denunciadoNick, u2.email AS denunciadoEmail, u2.urtImagen AS denunciadoUrlImagen, ed.nombre AS estadoNombre, ed.leida AS denunciaLeida, ed.aceptada AS denunciaAceptada, ed.rechazada AS denunciaRechazada FROM denuncia AS d INNER JOIN usuario AS u1 ON d.id = :idDenuncia AND d.denunciante = u1.id INNER JOIN usuario u2 ON d.denunciado = u2.id INNER JOIN estadodenuncia AS ed ON ed.id = d.estado;');
			$query->execute([
								'idDenuncia' => $denuncia
							]);
			$suscripcion = $query->fetch(PDO::FETCH_ASSOC);
			return $suscripcion;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function actualizarEstadoDenuncia($denuncia, $estado){
		$fecha = new DateTime();
		
		try{
			$query = $this->prepare('UPDATE denuncia SET estado = :estado WHERE id = :denuncia');
			$query->execute([	'estado' => $estado,
								'denuncia' => $denuncia]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function resolverDenuncia($denuncia, $administrador, $respuesta, $estado){
		$fecha = new DateTime();
		
		try{
			$query = $this->prepare('UPDATE denuncia SET resolvio = :administrador, fechaResolucion = :fechaResolucion, respuesta = :respuesta, estado = :estado WHERE id = :denuncia');
			$query->execute([	'administrador' => $administrador,
								'fechaResolucion' => $fecha->format('Y-m-d H:i:s'),
								'respuesta' => $respuesta,
								'estado' => $estado,
								'denuncia' => $denuncia]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setTyc($texto){
		try{
			$query = $this->prepare('UPDATE tyc SET tyc = :texto');
			$query->execute(['texto' => $texto]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}

}
?>