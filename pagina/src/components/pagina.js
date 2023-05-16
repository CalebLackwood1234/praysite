var PaginaCOMP = {
	usuario: {},
    oninit: function(vnode) {
		this.usuario.idUsuario = null;
		this.usuario.nombre = "";
		this.usuario.logueado = -1;
		this.usuario.avatar = "src/images/avatar.jpg";
		this.usuario.siguiendo = null;
		this.usuario.mensajes = null;
		
		var formData = new FormData;
		m.request({
			method: "POST",
			url: urlServicios + "logueado/checkear",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			console.log(data);
			if(data.error > 0) {
				PaginaCOMP.usuario.nombre = "Invitado";
				PaginaCOMP.usuario.logueado = 0;
				PaginaCOMP.usuario.puedePublicar = 0;
				PaginaCOMP.usuario.puedeAdministrar = 0;
				PaginaCOMP.usuario.idUsuario = null;
			} else {
				PaginaCOMP.usuario.idUsuario = data.idUsuario;
				PaginaCOMP.usuario.nombre = data.nombre;
				PaginaCOMP.usuario.email = data.email;
				if( data.urlImagen != null ) {
					PaginaCOMP.usuario.avatar = "../servicio/resources/image/perfiles/" + data.idUsuario + "/" + data.urlImagen + "_med.jpg";
				}
				PaginaCOMP.usuario.logueado = 1;
				PaginaCOMP.usuario.puedePublicar = data.puedePublicar;
				PaginaCOMP.usuario.puedeAdministrar = data.puedeAdministrar;
				LateralCOMP.actualizarSiguiendoMensajes();
				PaginaCOMP.actualizarMensajes();
				PaginaCOMP.actualizarSesion();
				PaginaCOMP.actualizarSiguiendo();
			}
			ContenidoCOMP.actualizarUrl();
		});
		
		LateralCOMP.oninit(vnode);
		CabeceraCOMP.oninit(vnode);
		ContenidoCOMP.oninit(vnode);
		
		window.addEventListener('hashchange', function() {
		  ContenidoCOMP.actualizarUrl();
		}, false)
    },
	actualizarSesionSetTime: null,
	actualizarSiguiendoSetTime: null,
    actualizarMensajes: function() {
		var formData = new FormData;
		m.request({
			method: "POST",
			url: urlServicios + "mensaje/listarChats",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			if(data == null) {
				PaginaCOMP.usuario.mensajes = [];
			} else {
				PaginaCOMP.usuario.mensajes = data;
			}
			LateralCOMP.actualizarSiguiendoMensajes();
		});
    },
	actualizarSesion: function() {
		clearTimeout(PaginaCOMP.actualizarSesionSetTime);
		PaginaCOMP.actualizarSesionSetTime = setTimeout(PaginaCOMP.actualizarSesion, 7000);
		var formData = new FormData;
		m.request({
			method: "POST",
			url: urlServicios + "logueado/checkear",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			if(data != null) {
				if(data.urlImagen == null) {
					PaginaCOMP.usuario.avatar = "src/images/avatar.jpg";
				} else {
					PaginaCOMP.usuario.avatar = "../servicio/resources/image/perfiles/" + data.idUsuario + "/" + data.urlImagen + "_med.jpg";
				}
				
				if(data.novedadesMensaje == 1) {
					PaginaCOMP.actualizarMensajes();
				}
			}
		});
    },
    actualizarSiguiendo: function() {
		clearTimeout(PaginaCOMP.actualizarSiguiendoSetTime);
		PaginaCOMP.actualizarSiguiendoSetTime = setTimeout(PaginaCOMP.actualizarSiguiendo, 15000);
		var formData = new FormData;
		m.request({
			method: "POST",
			url: urlServicios + "seguir/listarSiguiendo",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			if(data == null) {
				PaginaCOMP.usuario.siguiendo = [];
			} else {
				PaginaCOMP.usuario.siguiendo = data;
			}
			LateralCOMP.actualizarSiguiendoMensajes();
		});
    },
    cambiarUrl: function(url) {
		m.route.set(url);
		ContenidoCOMP.actualizarUrl();
    },
    oncreate: function(vnode) {
        LateralCOMP.oncreate(vnode);
		CabeceraCOMP.oncreate(vnode);
    },
    onupdate: function(vnode) {
		ContenidoCOMP.onupdate(vnode);
    },
    onbeforeupdate: function(newVnode, oldVnode) {
		ContenidoCOMP.onbeforeupdate(newVnode, oldVnode);
        return true;
    },
	/*
    onbeforeupdate: function(newVnode, oldVnode) {
        return true;
    },
    onupdate: function(vnode) {
        console.log("DOM updated");
    },
    onbeforeremove: function(vnode) {
        console.log("exit animation can start")
        return new Promise(function(resolve) {
            // call after animation completes
            resolve();
        })
    },
    onremove: function(vnode) {
        console.log("removing DOM element");
    },
	*/
    view: function(vnode) {
        return m("div#pagina", [
			m("div#lateral", LateralCOMP.view(vnode)),
			m("div#cabecera", CabeceraCOMP.view(vnode)),
			m("div#cotenido", ContenidoCOMP.view(vnode)),
		]);
    }
}