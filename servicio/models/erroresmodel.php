<?php

define('TIPO_ERROR_IGNORAR',			0 ); // No se debe mostrar mensaje de error
define('TIPO_ERROR_SIMPLE',				1 ); // Se muestra un mensaje en color rojo
define('TIPO_ERROR_POP_UP',				2 ); // Se mostrara un mensaje de error en un cuadro de aceptar
define('TIPO_ERROR_FATAL',				3 ); // Se envia al usuario a la pantalla de logueo
define('TIPO_EMAIL_SIN_VERIFICAR',		4 ); // Se envia al usuario a pantalla de instrucciones de envio de correo
define('TIPO_EMAIL_VERIFICADO',			5 ); // Se envia al usuario a pantalla de exito de verificacion de email
define('TIPO_SOLICITAR_BLANQUEO_PASS',	6 ); // Se envia al usuario a pantalla de instrucciones de blanqueo de password
define('TIPO_BLANQUEO_PASS_EXITOSO',	7 ); // Se envia al usuario a pantalla de exito de blanqueo de password
define('TIPO_DESLOGUEO',				8 ); // Se envia al usuario a pantalla de deslogueo exitoso
define('TIPO_LOGUEADO_HOME',			9 ); // El usuario se logueo y se lo envia a la home
define('TIPO_CAMBIO_EMAIL_EXITOSO',		10); // Se envia al usuario a pantalla de exito de cambio de email

define('CODIGO_ERROR',	400);
define('CODIGO_EXITO',	200);

class ErroresModel extends Model {

	private $id;
	private $back;
	private $front;
	private $tipo;
	private $datos;
	private $codigo;
	private $cabecera;
	
	public function setId($id){ $this->id = $id; }
	public function setBack($back){ $this->back = $back; }
	public function setFront($front){ $this->front = $front; }
	public function setTipo($tipo){ $this->tipo = $tipo; }
	public function setDatos($datos){ $this->datos = $datos; }
	public function setCodigo($codigo){ $this->codigo = $codigo; }
	public function setCabecera($cabecera){ $this->cabecera = $cabecera; }

	public function getId(){ return $this->id;}
	public function getBack(){ return $this->back; }
	public function getFront(){ return $this->front; }
	public function getTipo(){ return $this->tipo; }
	public function getDatos(){ return $this->datos; }
	public function getCodigo(){ return $this->codigo; }
	public function getCabecera(){ return $this->cabecera; }

	public function get($id){
		$this->id = $id;
		$this->tipo = constant('TIPO_ERROR_SIMPLE');
		$this->codigo = constant('CODIGO_ERROR');
		$this->cabecera = true;
		switch ($id) {
			case 101:
				$this->front = "No se ha definido un servicio";
				break;
			case 102:
				$this->front = "No se ha definido un servicio valido";
				break;
			case 103:
				$this->front = "No se ha definido un servicio existente";
				break;
			case 104:
				$this->front = "Faltan campos necesarios";
				break;
			case 105:
				$this->front = "Los campos tienen caracateres invalidos";
				break;
			case 106:
				$this->front = "El email tiene un formato incorrecto";
				break;
			case 107:
				$this->front = "Largo de algunos de los campos, menor al mínimo solicitado";
				break;
			case 108:
				$this->front = "Fecha invalida";
				break;
			case 109:
				$this->front = "No se pudo registrar el usuario";
				break;
			case 110:
				$this->front = "No se pudo encontrar al usuario";
				break;
			case 111:
				$this->front = "Email o nick ya utilizados";
				break;
			case 112:
				$this->front = "Email ya utilizado";
				break;
			case 113:
				$this->front = "Nick ya utilizado";
				break;
			case 114:
				$this->front = "Error al verificar el email, vuelva a intentarlo mas tarde";
				break;
			case 115:
				$this->front = "El código de verificación es incorrecto, por favor chequee sus emails";
				break;
			case 116:
				$this->front = "Error al intentar generar un codigo de verificacion, por favor volvear a intentarlo mas tarde";
				break;
			case 117:
				$this->front = "Código de verificación invalido, vuelva a generar uno";
				break;
			case 118:
				$this->front = "Error al cambiar de password, vuelva a intentarlo mas tarde";
				break;
			case 119:
				$this->front = "Usuario temporalmente bloqueado, vuelva a intentar loguearse en 20 minutos";
				break;
			case 120:
				$this->front = "Usuario o contraseña incorrectos";
				break;
			case 121:
				$this->front = "No se pudo iniciar sesion, vuelva a intentarlo mas tarde";
				break;
			case 122:
				$this->front = "Sesion no encontrada";
				break;
			case 123:
				$this->cabecera = false;
				$this->tipo = constant('TIPO_ERROR_FATAL');
				$this->front = "Error de sesion";
				break;
			case 124:
				$this->front = "Funcionalidad no permitida";
				break;
			case 125:
				$this->front = "Error al guardar";
				break;
			case 126:
				$this->front = "Error en le código de verificacion, vuelva a generar la solicitud";
				break;
			case 127:
				$this->front = "Usuario no verificado";
				break;
			case 128:
				$this->front = "No se puede modificar los parametros de un usuario administrador";
				break;
			case 129:
				$this->front = "Nombre de pagina duplicado";
				break;
			case 130:
				$this->front = "Página no encontrada";
				break;
			case 131:
				$this->front = "No encontrado";
				break;
			case 132:
				$this->front = "Error al consultar";
				break;
			case 133:
				$this->front = "Campo demasiado largo";
				break;
			case 134:
				$this->front = "No se puede borrar el tipo de suscripcion";
				break;
			case 135:
				$this->front = "No se puede borrar el tipo de suscripcion ya que cuenta con paginas que lo estan usando";
				break;
			case 136:
				$this->front = "No puedes acceder a la página";
				break;
			case 137:
				$this->front = "No tienes el nivel de suscripcion necesario para ver la página";
				break;
			case 138:
				$this->front = "No se puede iniciar una charla con uno mismo";
				break;
			case 139:
				$this->front = "No puedes enviarle mensajes al usuario";
				break;
			case 140:
				$this->front = "No puedes enviarle mensajes al usuario, tienes deshabilitados los chats";
				break;
			case 141:
				$this->front = "No puedes enviarle mensajes al usuario ya que lo has bloqueado";
				break;
			case 142:
				$this->front = "No puedes enviarle mensajes al usuario ya que tienes bloqueadas las cominucaciones con este tipo de usuario";
				break;
			case 143:
				$this->front = "Formato de imagen no soportado";
				break;
			case 144:
				$this->front = "Imagen muy grande, por favor seleccionar una imagen mas pequeña";
				break;
			case 145:
				$this->front = "Error al cargar la imagen, por favor vuelva a intentarlo";
				break;
			case 146:
				$this->front = "Se ha llegado al límite de imágenes por publicación";
				break;
			case 147:
				$this->front = "La publicación no tiene imagenes";
				break;
			case 148:
				$this->front = "Imagen no encontrada";
				break;
			case 149:
				$this->front = "Error al borrar la imagen, por favor vuelva a intentarlo";
				break;
			case 150:
				$this->front = "Password del usuario mal ingresado";
				break;
				
				
				
				
			// REDIRECCIONAMIENTOS EXITOSOS
			case 701:
				$this->codigo = constant('CODIGO_EXITO');
				$this->tipo = constant('TIPO_EMAIL_SIN_VERIFICAR');
				$this->front = "Se necesita verificar el email";
				break;
			case 702:
				$this->codigo = constant('CODIGO_EXITO');
				$this->tipo = constant('TIPO_EMAIL_VERIFICADO');
				$this->front = "Email verificado, por favor loguearse";
				break;
			case 703:
				$this->codigo = constant('CODIGO_EXITO');
				$this->tipo = constant('TIPO_EMAIL_VERIFICADO');
				$this->front = "Usuario ya verificado";
				break;
			case 704:
				$this->codigo = constant('CODIGO_EXITO');
				$this->tipo = constant('TIPO_EMAIL_VERIFICADO');
				$this->front = "Usuario ya verificado";
				break;
			case 705:
				$this->codigo = constant('CODIGO_EXITO');
				$this->tipo = constant('TIPO_SOLICITAR_BLANQUEO_PASS');
				$this->front = "Se envio un email para recuperar su password";
				break;
			case 706:
				$this->codigo = constant('CODIGO_EXITO');
				$this->tipo = constant('TIPO_BLANQUEO_PASS_EXITOSO');
				$this->front = "Su password ha sido actualizada exitosamente";
				break;
			case 707:
				$this->cabecera = false;
				$this->codigo = constant('CODIGO_EXITO');
				$this->tipo = constant('TIPO_DESLOGUEO');
				$this->front = "Su usuario se a podido desloguear exitosamente";
				break;
			case 708:
				$this->cabecera = false;
				$this->codigo = constant('CODIGO_EXITO');
				$this->tipo = constant('TIPO_LOGUEADO_HOME');
				$this->front = "Su usuario se a podido loguear exitosamente";
				break;
			case 709:
				$this->codigo = constant('CODIGO_EXITO');
				$this->tipo = constant('TIPO_CAMBIO_EMAIL_EXITOSO');
				$this->front = "Su email a sido actualizado exitosamente";
				break;
			default:
				$this->front = "Error generico";
				break;
		}
		$this->back = "Error:" . $id . " - " . $this->front;
	}
	
	public function render(){
		return array('error' => $this->id, 'errorMensaje' => $this->front, 'errorTipo' => $this->tipo, 'errorDatos' => $this->datos);
	}

}
?>