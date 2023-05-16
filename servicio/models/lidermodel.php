<?php

class LiderModel extends Model {

	public function __construct(){
		parent::__construct();
	}
	
	public function getLider($id){
		try{
			$query = $this->prepare('SELECT l.*, t.nombre AS tipoRelacionChatsNombre, t.descripcion AS tipoRelacionChatsDescripcion, t.deshabilitado AS tipoRelacionChatsDeshabilitado, t.soloCreados AS tipoRelacionChatsSoloCreados, t.suscripcionMinimaNivel AS tipoRelacionChatsSuscripcionMinimaNivel FROM lider AS l INNER JOIN tiporelacionchats AS t ON l.id = :id AND l.tiporelacionchats = t.id');
			$query->execute(['id' => $id]);
			$user = $query->fetch(PDO::FETCH_ASSOC);
			return $user;
		}catch(PDOException $e){
			return $this->returnError(131);
		}
	}
	
	public function listarTipoRelacionChats(){
		$items = [];

		try{
			$query = $this->prepare('SELECT * FROM tipoRelacionChats');
			$query->execute();

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setTipoRelacionChats($idLider, $idTipo){
		try{
			$query = $this->prepare('UPDATE lider SET tipoRelacionChats = :idTipo WHERE id = :idLider');
			$query->execute([	'idLider' => $idLider,
								'idTipo' => $idTipo]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setBiografia($idLider, $biografia){
		try{
			$query = $this->prepare('UPDATE lider SET biografia = :biografia WHERE id = :idLider');
			$query->execute([	'idLider' => $idLider,
								'biografia' => $biografia]);
			return true;
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
	
	public function listarTipoSuscripcionHabilitadas($idLider){
		$items = [];

		try{
			$query = $this->prepare('SELECT t.*, h.nombrePersonalizado FROM suscripcionhabilitada AS h INNER JOIN tiposuscripcion AS t ON h.lider = :idLider AND h.tipo = t.id');
			$query->execute(['idLider' => $idLider]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return $this->returnError(999);
		}
	}
	
	public function addTipoSuscripcionHabilitadas($idLider, $tipo, $nombrePersonalizado){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('INSERT INTO suscripcionhabilitada (lider, tipo, nombrePersonalizado) VALUES (:idLider, :tipo, :nombrePersonalizado)');
			$query->execute([
				'idLider' => $idLider,
				'tipo' => $tipo,
				'nombrePersonalizado' => $nombrePersonalizado
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}

    function borrarTipoSuscripcionHabilitadas($idLider, $tipo){
		try{
			$query = $this->prepare('DELETE FROM suscripcionhabilitada WHERE lider = :idLider AND tipo = :tipo');
			$query->execute([
				'idLider' => $idLider,
				'tipo' => $tipo
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
    }
	
	public function modificarTipoSuscripcionHabilitadas($idLider, $tipo, $nombrePersonalizado){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE suscripcionhabilitada SET nombrePersonalizado = :nombrePersonalizado WHERE lider = :idLider AND tipo = :tipo');
			$query->execute([
				'idLider' => $idLider,
				'tipo' => $tipo,
				'nombrePersonalizado' => $nombrePersonalizado
			]);
			if($query->rowCount()) return true;

			return false;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function listarPaginasLiderTipoSuscripcion($idLider, $tipoSuscripcion){
		$items = [];

		try{
			$query = $this->prepare('SELECT * FROM pagina WHERE lider = :idLider AND suscripcionMinimo = :tipoSuscripcion');
			$query->execute([	'idLider' => $idLider,
								'tipoSuscripcion' => $tipoSuscripcion
							]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;
		}catch(PDOException $e){
			return $this->returnError(999);
		}
	}

}
?>