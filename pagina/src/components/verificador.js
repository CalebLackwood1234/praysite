var VerficadorCOMP = {
	verificador: {},
    oninit: function(vnode) {
		$.urlParam = function(name){
			var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
			return results[1] || 0;
		}
		
		this.verificador.cargando = 1;
		this.verificador.error = 0;
		this.verificador.ingreso = 0;
		if( window.location.hash.search("#/verificarEmail") == 0 ) {
			var formData = new FormData;
			formData.append("email",	$.urlParam('email'));
			formData.append("codigo",	$.urlParam('codigo'));
			
			m.request({
				method: "POST",
				url: urlServicios + "deslogueado/verificarEmail",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				VerficadorCOMP.verificador.mensaje = data.errorMensaje;
				VerficadorCOMP.verificador.cargando = 0;
			})
			.catch(function(data) {
				VerficadorCOMP.verificador.mensaje = data.response.errorMensaje;
				VerficadorCOMP.verificador.cargando = 0;
				VerficadorCOMP.verificador.error = 1;
			});
		} else if( window.location.hash.search("#/cambiarEmail") == 0 ) {
			var formData = new FormData;
			formData.append("email",	$.urlParam('email'));
			formData.append("codigo",	$.urlParam('codigo'));
			
			m.request({
				method: "POST",
				url: urlServicios + "deslogueado/cambiarEmail",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				VerficadorCOMP.verificador.mensaje = "Email modificado exitosamente";
				VerficadorCOMP.verificador.cargando = 0;
			})
			.catch(function(data) {
				VerficadorCOMP.verificador.mensaje = data.response.errorMensaje;
				VerficadorCOMP.verificador.cargando = 0;
				VerficadorCOMP.verificador.error = 1;
			});
		} else if( window.location.hash.search("#/recuperarPassword") == 0 ) {
			VerficadorCOMP.verificador.cargando = 0;
			VerficadorCOMP.verificador.ingreso = 1;
		} else {
			this.verificador.cargando = 0;
			this.verificador.error = 1;
			this.verificador.mensaje = "Lamentablamente no pudimos identificar la pagina ingresada";
		}
    },
	passwordAccion: function() {
		if(validarCampos("#formPassword")) {
			if( $("#form_password_pass1").val() != $("#form_password_pass2").val() ) {
				M.toast({html: "El Password y el Repetir Password no coinciden", classes: 'rounded red'});
				$( "#form_password_pass2" ).removeClass( "valid" );
				$( "#form_password_pass2" ).addClass( "invalid" );
				return;
			}
			VerficadorCOMP.verificador.cargando = 1;
			
			var formData = new FormData;
			formData.append("email",	$.urlParam('email'));
			formData.append("codigo",	$.urlParam('codigo'));
			formData.append("pass",		$("#form_password_pass1").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "deslogueado/blanquearPassword",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				VerficadorCOMP.verificador.cargando = 0;
				VerficadorCOMP.verificador.ingreso = 0;
				VerficadorCOMP.verificador.mensaje = data.errorMensaje;
			})
			.catch(function(data) {
				VerficadorCOMP.verificador.cargando = 0;
				VerficadorCOMP.verificador.ingreso = 0;
				VerficadorCOMP.verificador.error = 1;
				VerficadorCOMP.verificador.mensaje = data.response.errorMensaje;
			})
		}
    },
	/*
    oncreate: function(vnode) {
        console.log("DOM created");
    },
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
        return m("div", {class: "valign-wrapper verificador_height"},
			m("div", {class: "center-align verificador_width"}, [
				m("p", {class: ""},
					m("img", {src: "src/images/logo.png"} , "")
				),
				(VerficadorCOMP.verificador.cargando == 1 ?
					m("p", {class: ""},
						m("img", {class: "cargando", src: urlCargando} , "")
					)
				:
					m("p", {class: VerficadorCOMP.verificador.error == 1 ? "red" : "green"}, VerficadorCOMP.verificador.mensaje)
				),
					VerficadorCOMP.verificador.ingreso == 1 ?
						m("div", {class: "row", id: "formPassword"},
							m("div", {class: "card col s12 offset-l3 l6 while"},[
								m("span", {class: "card-title"}, "Ingrese su nuevo Password"),
								m("div", {class: "row"},[
									m("div", {class: "input-field col s12 m6"},[
										m("i", {class: "material-icons prefix"}, "settings_ethernet"),
										m("input", {id: "form_password_pass1", type:"password", class:"validate requerido"}, ""),
										m("label", {for: "form_password_pass1"}, "Password *"),
									]),
									m("div", {class: "input-field col s12 m6"},[
										m("i", {class: "material-icons prefix"}, "settings_ethernet"),
										m("input", {id: "form_password_pass2", type:"password", class:"validate requerido"}, ""),
										m("label", {for: "form_password_pass2"}, "Repetir Password *"),
									]),
								]),
								m("div", {class: "row"},[
									m("a", {class: "waves-effect waves-green btn-flat modal_boton right", href:"javascript:VerficadorCOMP.passwordAccion();"}, "Enviar"),
								]),
							])
						)
					:
						m("a", {href: urlWeb}, 
							m("div", {class: "waves-effect waves-light btn grey darken-1"}, [
								m("i", {class: "material-icons large left"}, "arrow_back"),"Volver"
							]),
						)
			])
		);
    }
}