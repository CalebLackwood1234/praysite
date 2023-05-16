<?php

class Session{
	
	function __construct( $db ){
        $this->db = $db;
    }
	
	public function obtenerSession(){
		if($this->existeCookie('idSesion') && $this->existeCookie('passSesion') && $this->existeCookie('hashId')) {
			$idSesion_in = $this->obtenerCookie('idSesion');
			$passSesion_in = $this->obtenerCookie('passSesion');
			$hashId_in = $this->obtenerCookie('hashId');
			$fecha = new DateTime();
			$fecha_in = $fecha->format('Y-m-d H:i:s');

			try{
				$consulta = $this->procedure('CALL sp_chequear_loguin(:idSesion_in, :passSesion_in, :hashId_in, :fecha_in, @id_out, @nick_out, @nombre_out, @email_out, @fechaAlta_out, @fechaUltimoLogueo_out, @fechaUltimaAccion_out, @novedadesMensaje_out, @fechaNacimiento_out, @tipoUsuario_out, @urlImagen_out, @puedeAdministrar_out, @puedePublicar_out, @error_out)',
					['idSesion_in' => $idSesion_in,
					'passSesion_in' => $passSesion_in,
					'hashId_in' => $hashId_in,
					'fecha_in' => $fecha_in],
					'SELECT @id_out AS idUsuario, @nick_out AS nick, @nombre_out AS nombre, @email_out AS email, @fechaAlta_out AS fechaAlta, @fechaUltimoLogueo_out AS fechaUltimoLogue, @fechaUltimaAccion_out AS fechaUltimaAccion, @novedadesMensaje_out AS novedadesMensaje, @fechaNacimiento_out AS fechaNacimiento, @tipoUsuario_out AS tipoUsuario, @urlImagen_out AS urlImagen, @puedeAdministrar_out AS puedeAdministrar, @puedePublicar_out AS puedePublicar, @error_out AS error;');
				
				if($consulta['error'] != 0) {
					$this->setearCookie('idSesion', '');
					$this->setearCookie('passSesion', '');
					$this->setearCookie('hashId', '');
					return $this->returnError(123);
				}

				$consulta['idSesion'] = (int) $idSesion_in;
				return $consulta;
			}catch(PDOException $e){
				$this->setearCookie('idSesion', '');
				$this->setearCookie('passSesion', '');
				$this->setearCookie('hashId', '');
				return $this->returnError(123);
			}
		} else {
			return $this->returnError(707);
		}
	}
	
	public function obtenerSessionSinErrores(){
		if($this->existeCookie('idSesion') && $this->existeCookie('passSesion') && $this->existeCookie('hashId')) {
			$idSesion_in = $this->obtenerCookie('idSesion');
			$passSesion_in = $this->obtenerCookie('passSesion');
			$hashId_in = $this->obtenerCookie('hashId');
			$fecha = new DateTime();
			$fecha_in = $fecha->format('Y-m-d H:i:s');

			try{
				$consulta = $this->procedure('CALL sp_chequear_loguin(:idSesion_in, :passSesion_in, :hashId_in, :fecha_in, @id_out, @nick_out, @nombre_out, @email_out, @fechaAlta_out, @fechaUltimoLogueo_out, @fechaUltimaAccion_out, @novedadesMensaje_out, @fechaNacimiento_out, @tipoUsuario_out, @urlImagen_out, @puedeAdministrar_out, @puedePublicar_out, @error_out)',
					['idSesion_in' => $idSesion_in,
					'passSesion_in' => $passSesion_in,
					'hashId_in' => $hashId_in,
					'fecha_in' => $fecha_in],
					'SELECT @id_out AS idUsuario, @nick_out AS nick, @nombre_out AS nombre, @email_out AS email, @fechaAlta_out AS fechaAlta, @fechaUltimoLogueo_out AS fechaUltimoLogue, @fechaUltimaAccion_out AS fechaUltimaAccion, @novedadesMensaje_out AS novedadesMensaje, @fechaNacimiento_out AS fechaNacimiento, @tipoUsuario_out AS tipoUsuario, @urlImagen_out AS urlImagen, @puedeAdministrar_out AS puedeAdministrar, @puedePublicar_out AS puedePublicar, @error_out AS error;');
				
				if($consulta['error'] != 0) {
					$this->setearCookie('idSesion', '');
					$this->setearCookie('passSesion', '');
					$this->setearCookie('hashId', '');
					return false;
				}

				$consulta['idSesion'] = (int) $idSesion_in;
				return $consulta;
			}catch(PDOException $e){
				$this->setearCookie('idSesion', '');
				$this->setearCookie('passSesion', '');
				$this->setearCookie('hashId', '');
				return false;
			}
		} else {
			return false;
		}
	}

	public function cerrarSession( $idSesion, $idUsuario ){
		if( ! $this->borrarSesionModel($idSesion, $idUsuario) ) return false;
			
		$this->setearCookie('idSesion', '');
		$this->setearCookie('passSesion', '');
		$this->setearCookie('hashId', '');
		return true;
	}

	public function crearSession( $nick, $email, $id, $semillaSesion ){
		$hashId = encriptadoMd5Azar($nick . $email);
		$passSesion = encriptadoMd5Azar($nick . $email);
		$hashSesion = encriptadoMd5($passSesion . $semillaSesion);
					
		if ($this->checkError($this->crearSesionModel($id, $hashSesion, $hashId)));
		$respuesta = $this->obtenerSesionModel($id, $hashSesion);
		
		$this->setearCookie("idSesion", $respuesta['id']);
		$this->setearCookie("passSesion", $passSesion);
		$this->setearCookie("hashId", $hashId);
	}

	public function existeCookie($nombre){
		if(!isset($_COOKIE[$nombre]) || $_COOKIE[$nombre] ==  NULL){
			return false;
		}
		return true;
	}

	public function obtenerCookie($nombre){
		return $_COOKIE[$nombre];
	}

	public function setearCookie($nombre, $datos){
		setcookie($nombre, $datos, time()+160000000, constant('SESSION_PATH'), constant('SESSION_DOMAIN'));
	}

	private function borrarSesionModel($id, $usuario){
		try{
			$query = $this->prepare('DELETE FROM sesioniniciada WHERE id = :id and usuario = :usuario');
			$query->execute([	'id' => $id,
								'usuario' => $usuario]);
			return true;
		}catch(PDOException $e){
			return false;
		}
	}
		
	private function crearSesionModel($usuario, $hashSesion, $hashId){
		try{
			$fecha = new DateTime();
			
			$query = $this->prepare('INSERT INTO sesioniniciada (usuario, hashSesion, hashId, fechaCreacion, fechaUltimaAccion) VALUES(:usuario, :hashSesion, :hashId, :fechaCreacion, :fechaUltimaAccion)');
			$query->execute([
				'usuario' => $usuario,
				'hashSesion' => $hashSesion,
				'hashId' => $hashId,
				'fechaCreacion' => $fecha->format('Y-m-d H:i:s'),
				'fechaUltimaAccion' => $fecha->format('Y-m-d H:i:s')
			]);
			
			$query2 = $this->prepare('UPDATE usuario SET fechaUltimoLogueo = :fechaUltimoLogueo, fechaUltimaAccion = :fechaUltimaAccion WHERE id = :id');
			$query2->execute([	'id' => $usuario,
								'fechaUltimoLogueo' => $fecha->format('Y-m-d H:i:s'),
								'fechaUltimaAccion' => $fecha->format('Y-m-d H:i:s')
			]);
			
			if($query->rowCount()) return null;

			return $this->returnError(121);
		}catch(PDOException $e){
			return $this->returnError(121);
		}
	}

	private function obtenerSesionModel($usuario, $hashSesion){
		try{
			$query = $this->prepare('SELECT id FROM sesioniniciada where usuario = :usuario and hashSesion = :hashSesion');
			$query->execute([
				'usuario' => $usuario,
				'hashSesion' => $hashSesion
			]);
			$response = $query->fetch(PDO::FETCH_ASSOC);
			return $response;

			return $this->returnError(121);
		}catch(PDOException $e){
			return $this->returnError(121);
		}
	}

    private function checkError($valor){
        if ($valor != NULL && is_array($valor) && array_key_exists('error', $valor) && $valor['error'] == true) {
            new Errores($valor['errorId']);
            return true;
        } else {
            return false;
        }
    }
	
	private function prepare($query){
        return $this->db->connect()->prepare($query);
    }
	
	private function procedure($prepare, $execute, $select){
        $conexion = $this->db->connect();
        $prepare = $conexion->prepare($prepare);
        $prepare->execute($execute);
        if ( $select == NULL ) {
            return;
        }
        $query = $conexion->query($select);
        return $query->fetch(PDO::FETCH_ASSOC);
    }
	
    private function returnError($id, $datos = NULL){
        return array('error' => true, 'errorId' => $id, 'errorDatos' => $datos);
    }

}

?>