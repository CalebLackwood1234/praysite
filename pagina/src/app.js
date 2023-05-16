var root = document.body;
var urlServicios = "http://localhost/praysite/servicio/";
var urlWeb = "http://localhost/praysite/pagina/";
var urlCargando = "src/images/cargando.gif"

m.route.prefix = '#';

m.route(root, "/", {
  "/": {
    onmatch() {
		m.route.set("/novedades");
    }
  },
  /*
  "/login": LoginCOMP,
  "/registrar": RegistrarCOMP,
  "/home": HomeCOMP,
  */
  "/verificarEmail": VerficadorCOMP,
  "/cambiarEmail": VerficadorCOMP,
  "/recuperarPassword": VerficadorCOMP,
  "/chat": PaginaCOMP,
  "/siguiendo": PaginaCOMP,
  "/lider": PaginaCOMP,
  "/novedades": PaginaCOMP,
  "/busqueda": PaginaCOMP,
  "/conversaciones": PaginaCOMP,
  "/modificar": PaginaCOMP,
  "/pagina": PaginaCOMP,
  "/admin": PaginaCOMP
});

function abrirModal(nombre) {
	$(nombre).modal();
	var instance = M.Modal.getInstance($(nombre));
	instance.open();
}

function cerrarModal(nombre) {
	var instance = M.Modal.getInstance($(nombre));
	instance.close();
}

function abrirMenu(nombre) {
	$(nombre).dropdown();
	var instance = M.Dropdown.getInstance($(nombre));
	instance.open();
}

function cerrarMenu(nombre) {
	var instance = M.Modal.getInstance($(nombre));
	instance.close();
}

function validarCampos(nombre) {
	var valido = true;
	
	var campos = $(nombre + ' .requerido');
	$.each( campos, function( index, entrada ) {
		if( entrada.value == null || entrada.value == "" ) {
			valido = false;
			$( "#" + entrada.id ).removeClass( "valid" );
			$( "#" + entrada.id ).addClass( "invalid" );
		}
	});
	
	if( !valido ) {
		M.toast({html: "Complete los campos obligatorios", classes: 'rounded red'});
		return valido;
	}
	
	return valido;
}

function parametroUrl(name) {
	try {
		var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
		return results[1] || 0;
	} catch (error) {
		return null;
	}
}

function prepararFecha(fecha) {
	if(fecha.length > 0) {
		var fechaArray = fecha.split("-");
		return fechaArray[2] + "-" + fechaArray[1] + "-" + fechaArray[0];
	} else {
		return "";
	}
}
