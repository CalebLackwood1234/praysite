<?php

if (!defined('INTENTOS_CHEQUEAR_EMAIL'))	define('INTENTOS_CHEQUEAR_EMAIL',	20);
if (!defined('INTENTOS_PASSWORD'))			define('INTENTOS_PASSWORD',			10);

class DeslogueadoModel extends Model {

	public function __construct(){
		parent::__construct();
	}
	
	public function registrar($nick, $nickSimplificado, $nombre, $nombreSimplificado, $email, $hashPass, $semillaPass, $semillaSesion, $codigoVerificacion, $estaVerificado, $estaBloqueado, $tipoUsuario, $fechaNacimiento){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('INSERT INTO usuario (nick, nickSimplificado, nombre, nombreSimplificado, email, hashPass, semillaPass, semillaSesion, codigoVerificacion, tipoCodigoVerificacion, estaVerificado, estaBloqueado, tipoUsuario, fechaNacimiento, intentos, fechaAlta) VALUES(:nick, :nickSimplificado, :nombre, :nombreSimplificado, :email, :hashPass, :semillaPass, :semillaSesion, :codigoVerificacion, :tipoCodigoVerificacion, :estaVerificado, :estaBloqueado, :tipoUsuario, :fechaNacimiento, :intentos, :fechaAlta)');
			$query->execute([
				'nick' => $nick,
				'nickSimplificado' => $nickSimplificado,
				'nombre' => $nombre,
				'nombreSimplificado' => $nombreSimplificado,
				'email' => $email,
				'hashPass' => $hashPass,
				'semillaPass' => $semillaPass,
				'semillaSesion' => $semillaSesion,
				'codigoVerificacion' => $codigoVerificacion,
				'tipoCodigoVerificacion' => 'R', // Registar usuario
				'estaVerificado' => $estaVerificado,
				'estaBloqueado' => $estaBloqueado,
				'tipoUsuario' => $tipoUsuario,
				'fechaNacimiento' => $fechaNacimiento,
				'intentos' => constant('INTENTOS_CHEQUEAR_EMAIL'),
				'fechaAlta' => $fecha->format('Y-m-d H:i:s')
			]);
			if($query->rowCount()) return null;

			return $this->returnError(109);
		}catch(PDOException $e){
			return $this->returnError(109);
		}
	}
	
	public function getUsuarioId($id){
		try{
			$query = $this->prepare('SELECT * FROM usuario WHERE id = :id');
			$query->execute(['id' => $id]);
			$user = $query->fetch(PDO::FETCH_ASSOC);
			return $user;
		}catch(PDOException $e){
			return $this->returnError(110);
		}
	}
	
	public function getTyc(){
		try{
			$query = $this->prepare('SELECT * FROM tyc');
			$query->execute();
			$request = $query->fetch(PDO::FETCH_ASSOC);
			return $request;
		}catch(PDOException $e){
			return $this->returnError(132);
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
	
	public function borrarUsuarioSinVerificar($nick, $email){
		try{
			$query = $this->prepare('DELETE FROM usuario WHERE estaVerificado = 0 AND (nick = :nick OR email = :email)');
			$query->execute([	'nick' => $nick,
								'email' => $email]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function borrarUsuarioSinVerificarSinIntentos($email){
		try{
			$query = $this->prepare('DELETE FROM usuario WHERE estaVerificado = 0 AND intentos <= 0 AND email = :email');
			$query->execute(['email' => $email]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
	
	public function buscarUsuario($nick, $email){
		$items = [];

		try{
			$query = $this->prepare('SELECT * FROM usuario WHERE nick = :nick OR email = :email');
			$query->execute([	'nick' => $nick,
								'email' => $email]);

			while($p = $query->fetch(PDO::FETCH_ASSOC)){
				array_push($items, $p);
			}

			return $items;

		}catch(PDOException $e){
			return false;
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
	
	public function setUsuarioVerificarEmail($id){
		try{
			$query = $this->prepare('UPDATE usuario SET intentos = ' . constant('INTENTOS_PASSWORD') . ', estaVerificado = 1, codigoVerificacion = null, tipoCodigoVerificacion = null WHERE id = :id');
			$query->execute(['id' => $id]);
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
	
	public function setEmail($id, $email){
		try{
			$query = $this->prepare('UPDATE usuario SET email = :email, emailPropuesto = NULL WHERE id = :id');
			$query->execute([	'id' => $id,
								'email' => $email]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}

}
?>