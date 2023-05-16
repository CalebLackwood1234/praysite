<?php

class LogueadoModel extends Model {

	public function __construct(){
		parent::__construct();
	}

	public function setNombre($idUsuario, $nombre, $nombreSimplificado){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('UPDATE usuario SET nombre = :nombre, nombreSimplificado = :nombreSimplificado WHERE id = :idUsuario');
			$query->execute([	'idUsuario' => $idUsuario,
								'nombre' => $nombre,
								'nombreSimplificado' => $nombreSimplificado]);
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
	
	public function setUsuarioIntentos($id, $intentos){
		try{
			$query = $this->prepare('UPDATE usuario SET intentos = :intentos WHERE id = :id');
			$query->execute([	'id' => $id,
								'intentos' => $intentos]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function setUsuarioFechaReinicioIntentos($id, $fechaReinicioIntentos){
		try{
			$query = $this->prepare('UPDATE usuario SET fechaReinicioIntentos = :fechaReinicioIntentos WHERE id = :id');
			$query->execute([	'id' => $id,
								'fechaReinicioIntentos' => $fechaReinicioIntentos]);
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
	
	public function setUsuarioUrlImagen($id, $urlImagen){
		try{
			$query = $this->prepare('UPDATE usuario SET urtImagen = :urlImagen WHERE id = :id');
			$query->execute([	'id' => $id,
								'urlImagen' => $urlImagen]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
}
?>