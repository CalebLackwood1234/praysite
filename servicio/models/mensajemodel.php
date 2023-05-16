<?php

if (!defined('MENSAJE_VISTO'))				define('MENSAJE_VISTO', 1);
if (!defined('SIN_MENSAJES'))				define('SIN_MENSAJES', 0);
if (!defined('ESTADO_CHAT_NORMAL'))			define('ESTADO_CHAT_NORMAL', 0);
if (!defined('ESTADO_DENUNCIA_INICIADA'))	define('ESTADO_DENUNCIA_INICIADA', 1);

class MensajeModel extends Model {

	public function __construct(){
		parent::__construct();
	}

	public function relacionDeChats($usuario1, $usuario2){
		$items = [];

		try{
			$query = $this->prepare('SELECT l.id, t.deshabilitado, t.soloCreados, t.suscripcionMinimaNivel FROM lider AS l INNER JOIN tiporelacionchats AS t ON (l.id = :idLider1 OR l.id = :idLider2) AND l.tipoRelacionChats = t.id AND l.habilitado = 1');
			$query->execute([
								'idLider1' => $usuario1,
								'idLider2' => $usuario2
							]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getChat($usuarioMenor, $usuarioMayor){
		try{
			$query = $this->prepare('SELECT * FROM chat WHERE usuarioIdMenor = :usuarioMenor AND usuarioIdMayor = :usuarioMayor');
			$query->execute([	'usuarioMenor' => $usuarioMenor,
								'usuarioMayor' => $usuarioMayor]);
			$chat = $query->fetch(PDO::FETCH_ASSOC);
			return $chat;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function addChat($usuarioIdMenor, $usuarioIdMayor){
		try{
			$query = $this->prepare('INSERT INTO chat (usuarioIdMenor, usuarioIdMayor, fueVisto, cantidadMensajes, estadoChatIdMenor, estadoChatIdMayor) VALUES (:usuarioIdMenor, :usuarioIdMayor, :fueVisto, :cantidadMensajes, :estadoChatIdMenor, :estadoChatIdMayor)');
			$query->execute([
				'usuarioIdMenor' => $usuarioIdMenor,
				'usuarioIdMayor' => $usuarioIdMayor,
				'fueVisto' => constant('MENSAJE_VISTO'),
				'cantidadMensajes' => constant('SIN_MENSAJES'),
				'estadoChatIdMenor' => constant('ESTADO_CHAT_NORMAL'),
				'estadoChatIdMayor' => constant('ESTADO_CHAT_NORMAL')
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function addMensaje($chat, $usuario, $mensaje){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('INSERT INTO mensaje (chat, usuario, fechaAlta, mensaje) VALUES (:chat, :usuario, :fechaAlta, :mensaje)');
			$query->execute([
				'chat' => $chat,
				'usuario' => $usuario,
				'fechaAlta' => $fecha->format('Y-m-d H:i:s'),
				'mensaje' => $mensaje
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function updateChat($chat, $cantidadMensajes, $preview, $ultimoUsuario){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE chat SET cantidadMensajes = :cantidadMensajes, fechaUltimaActualizacion = :fechaUltimaActualizacion, previewUltimoMensaje = :previewUltimoMensaje, previewUltimoMensajeUsuarioId = :previewUltimoMensajeUsuarioId, fueVisto = 0 WHERE id = :id');
			$query->execute([
				'cantidadMensajes' => $cantidadMensajes,
				'fechaUltimaActualizacion' => $fecha->format('Y-m-d H:i:s'),
				'previewUltimoMensaje' => $preview,
				'previewUltimoMensajeUsuarioId' => $ultimoUsuario,
				'id' => $chat
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function updateMensajeUsuario($idUsuario, $novedad){
		try{
			$query = $this->prepare('UPDATE usuario SET novedadesMensaje = :novedad WHERE id = :idUsuario');
			$query->execute([
				'novedad' => $novedad,
				'idUsuario' => $idUsuario
			]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getSuscripcion($usuario, $lider){
		try{
			$query = $this->prepare('SELECT * FROM suscripcion WHERE usuario = :usuario AND lider = :lider AND problemaDePago = 0');
			$query->execute([	'usuario' => $usuario,
								'lider' => $lider]);
			$suscripcion = $query->fetch(PDO::FETCH_ASSOC);
			return $suscripcion;
		}catch(PDOException $e){
			return false;
		}
	}

	public function getUltimosChats($usuario, $maximoPagina, $fechaMinima){
		$items = [];

		try{
			$query = $this->prepare('SELECT c.*, c.fueVisto = 1 OR :usuario3 = c.previewUltimoMensajeUsuarioId AS fueVistoPorMi, if(:usuario4 = c.usuarioIdMenor, c.usuarioIdMayor, c.usuarioIdMenor) AS otroParticipante, if(:usuario5 = c.usuarioIdMenor, u2.nombre, u1.nombre) AS otroParticipanteNombre , if(:usuario6 = c.usuarioIdMenor, u2.nick, u1.nick) AS otroParticipanteNick , if(:usuario7 = c.usuarioIdMenor, u2.urtImagen, u1.urtImagen) AS otroParticipanteUrlImagen from chat AS c INNER JOIN usuario AS u1 ON (c.usuarioIdMenor = :usuario1 OR c.usuarioIdMayor = :usuario2) AND c.fechaUltimaActualizacion <= :fechaMinima AND u1.id = c.usuarioIdMenor INNER JOIN usuario AS u2 ON u2.id = c.usuarioIdMayor ORDER BY c.fechaUltimaActualizacion DESC LIMIT 0, :maximoPagina;');
			$query->execute([
								'usuario1' => $usuario,
								'usuario2' => $usuario,
								'usuario3' => $usuario,
								'usuario4' => $usuario,
								'usuario5' => $usuario,
								'usuario6' => $usuario,
								'usuario7' => $usuario,
								'fechaMinima' => $fechaMinima,
								'maximoPagina' => $maximoPagina
							]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}

	public function getChatsTodos($usuario){
		$items = [];

		try{
			$query = $this->prepare('SELECT c.*, c.fueVisto = 1 OR :usuario3 = c.previewUltimoMensajeUsuarioId AS fueVistoPorMi, if(:usuario4 = c.usuarioIdMenor, c.usuarioIdMayor, c.usuarioIdMenor) AS otroParticipante, if(:usuario5 = c.usuarioIdMenor, u2.nombre, u1.nombre) AS otroParticipanteNombre , if(:usuario6 = c.usuarioIdMenor, u2.nick, u1.nick) AS otroParticipanteNick , if(:usuario7 = c.usuarioIdMenor, u2.urtImagen, u1.urtImagen) AS otroParticipanteUrlImagen from chat AS c INNER JOIN usuario AS u1 ON (c.usuarioIdMenor = :usuario1 OR c.usuarioIdMayor = :usuario2) AND u1.id = c.usuarioIdMenor INNER JOIN usuario AS u2 ON u2.id = c.usuarioIdMayor ORDER BY c.fechaUltimaActualizacion DESC');
			$query->execute([
								'usuario1' => $usuario,
								'usuario2' => $usuario,
								'usuario3' => $usuario,
								'usuario4' => $usuario,
								'usuario5' => $usuario,
								'usuario6' => $usuario,
								'usuario7' => $usuario
							]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function getChatIdUsuario($idChat, $usuario){
		try{
			$query = $this->prepare('SELECT * FROM chat WHERE id = :idChat AND (usuarioIdMayor = :usuario1 || usuarioIdMenor = :usuario2)');
			$query->execute([	'usuario1' => $usuario,
								'usuario2' => $usuario,
								'idChat' => $idChat]);
			$chat = $query->fetch(PDO::FETCH_ASSOC);
			return $chat;
		}catch(PDOException $e){
			return false;
		}
	}

	public function getUltimosMensajes($idChat, $idUsuarioOtro, $idMensajeMinimo, $idMensajeMaximo, $maximoPagina){
		$items = [];

		try{
			$query = $this->prepare('SELECT m.*, u.urtImagen, u.nombre, u.nick, u.id AS idOtro from mensaje AS m INNER JOIN usuario AS u ON m.chat = :idChat AND (:idMensajeMinimo1 = -1 OR m.id < :idMensajeMinimo2 || m.id > :idMensajeMaximo) AND u.id = :idUsuarioOtro ORDER BY m.id DESC LIMIT 0, :maximoPagina;');
			//$query = $this->prepare('SELECT m.* from mensaje AS m INNER JOIN usuario AS u ON m.chat = :idChat AND (:idMensajeMinimo1 = -1 OR id < :idMensajeMinimo2 || id > :idMensajeMaximo) AND u.id = :idUsuarioOtro ORDER BY m.id DESC LIMIT 0, :maximoPagina;');
			$query->execute([
								'idChat' => $idChat,
								'idUsuarioOtro' => $idUsuarioOtro,
								'idMensajeMinimo1' => $idMensajeMinimo,
								'idMensajeMinimo2' => $idMensajeMinimo,
								'idMensajeMaximo' => $idMensajeMaximo,
								'maximoPagina' => $maximoPagina
							]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}

	public function getUltimosMensajesNuevos($idChat, $idUsuarioOtro, $idMensajeMaximo){
		$items = [];

		try{
			$query = $this->prepare('SELECT m.*, u.urtImagen, u.nombre, u.nick, u.id AS idOtro from mensaje AS m INNER JOIN usuario AS u ON m.chat = :idChat AND m.id > :idMensajeMaximo AND u.id = :idUsuarioOtro ORDER BY m.id DESC;');
			$query->execute([
								'idChat' => $idChat,
								'idUsuarioOtro' => $idUsuarioOtro,
								'idMensajeMaximo' => $idMensajeMaximo
							]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function updateVistoChat($idChat){
		try{
			$query = $this->prepare('UPDATE chat SET fueVisto = 1 WHERE id = :idChat');
			$query->execute([
				'idChat' => $idChat
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function updateChatBloqueoIdMenor($idChat, $estadoBloqueo){
		try{
			$query = $this->prepare('UPDATE chat SET estadoChatIdMenor = :estadoBloqueo WHERE id = :idChat');
			$query->execute([
				'idChat' => $idChat,
				'estadoBloqueo' => $estadoBloqueo
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function updateChatBloqueoIdMayor($idChat, $estadoBloqueo){
		try{
			$query = $this->prepare('UPDATE chat SET estadoChatIdMayor = :estadoBloqueo WHERE id = :idChat');
			$query->execute([
				'idChat' => $idChat,
				'estadoBloqueo' => $estadoBloqueo
			]);
			if($query->rowCount()) return true;

			return false;
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
	
	public function buscarChat($idUsuario1, $idUsuario2){
		try{
			$query = $this->prepare('SELECT * FROM chat WHERE (usuarioIdMenor = :idUsuario1_1 and usuarioIdMayor = :idUsuario2_1) or (usuarioIdMenor = :idUsuario2_2 and usuarioIdMayor = :idUsuario1_2)');
			$query->execute([
								'idUsuario1_1' => $idUsuario1,
								'idUsuario2_1' => $idUsuario2,
								'idUsuario2_2' => $idUsuario2,
								'idUsuario1_2' => $idUsuario1
							]);
			$user = $query->fetch(PDO::FETCH_ASSOC);
			return $user;
		}catch(PDOException $e){
			return $this->returnError(132);
		}
	}

}
?>