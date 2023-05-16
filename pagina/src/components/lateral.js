var LateralCOMP = {
	oninit: function(vnode) {
		$('.dropdown-trigger').dropdown();
    },
	actualizarSiguiendoMensajes: function() {
		if( PaginaCOMP.usuario.logueado == 1 ) {
			if(PaginaCOMP.usuario.siguiendo == null) {
				m.render(document.getElementById("lateralSiguiendo"), m("img", {class: "cargando", style: "margin: 5px;", src: urlCargando} , ""));
			} else {
				var listaSiguiendo = [];
				if(PaginaCOMP.usuario.siguiendo.length == 0) {
					listaSiguiendo.push(m("div", "No sigues ninguna página"));
				} else {
					for (i = 0; i < PaginaCOMP.usuario.siguiendo.length; i++) {
						claseNovedad = PaginaCOMP.usuario.siguiendo[i].novedades == 1 ? " novedad" : "";
						if(PaginaCOMP.usuario.siguiendo[i] != null && PaginaCOMP.usuario.siguiendo[i].LiderUrlImagen != null) {
							listaSiguiendo.push(m("a", {class: "collection-item waves-effect", href:"javascript:PaginaCOMP.cambiarUrl('/siguiendo?id=" + PaginaCOMP.usuario.siguiendo[i].id + "&lider=" + PaginaCOMP.usuario.siguiendo[i].lider + "')"}, [
								m("img", {class: "circle avatar_mini left", src: "../servicio/resources/image/perfiles/" + PaginaCOMP.usuario.siguiendo[i].lider + "/" + PaginaCOMP.usuario.siguiendo[i].LiderUrlImagen + "_mini.jpg"}, ""),
								m("div", {class: "lateral_list_persona"}, PaginaCOMP.usuario.siguiendo[i].liderNombre),
								m("div", {class: "lateral_list_detalle" + claseNovedad}, PaginaCOMP.usuario.siguiendo[i].nombre),
							]));
						} else {
							listaSiguiendo.push(m("a", {class: "collection-item waves-effect", href:"javascript:PaginaCOMP.cambiarUrl('/siguiendo?id=" + PaginaCOMP.usuario.siguiendo[i].id + "&lider=" + PaginaCOMP.usuario.siguiendo[i].lider + "')"}, [
								m("img", {class: "circle avatar_mini left", src: "src/images/avatar.jpg"}, ""),
								m("div", {class: "lateral_list_persona"}, PaginaCOMP.usuario.siguiendo[i].liderNombre),
								m("div", {class: "lateral_list_detalle" + claseNovedad}, PaginaCOMP.usuario.siguiendo[i].nombre),
							]));
						}
					} 
				}
				m.render(document.getElementById("lateralSiguiendo"), m("div", {class: "collection lateral_menu_list"}, listaSiguiendo));
			}
			if(PaginaCOMP.usuario.mensajes == null) {
				m.render(document.getElementById("lateralMensaje"), m("img", {class: "cargando", style: "margin-top: 0px;",src: urlCargando} , ""));
			} else {
				var listaMensajes = [];
				if(PaginaCOMP.usuario.mensajes.length == 0) {
					listaMensajes.push(m("div", "No tienes mensajes"));
				} else {
					for (i = 0; i < PaginaCOMP.usuario.mensajes.length; i++) {
						claseNovedad = PaginaCOMP.usuario.mensajes[i].fueVistoPorMi == 0 ? " novedad" : "";
						mensajePropio = PaginaCOMP.usuario.mensajes[i].previewUltimoMensajeUsuarioId == PaginaCOMP.usuario.mensajes[i].otroParticipante ? "" : "Tu: ";
						if(PaginaCOMP.usuario.mensajes[i] != null && PaginaCOMP.usuario.mensajes[i].otroParticipanteUrlImagen != null) {
							listaMensajes.push(m("a", {class: "collection-item waves-effect", href:"javascript:PaginaCOMP.cambiarUrl('/chat?id=" + PaginaCOMP.usuario.mensajes[i].id + "')"}, [
								m("img", {class: "circle avatar_mini left", src: "../servicio/resources/image/perfiles/" + PaginaCOMP.usuario.mensajes[i].otroParticipante + "/" + PaginaCOMP.usuario.mensajes[i].otroParticipanteUrlImagen + "_mini.jpg"}, ""),
								m("div", {class: "lateral_list_persona"}, PaginaCOMP.usuario.mensajes[i].otroParticipanteNombre),
								m("div", {class: "lateral_list_detalle" + claseNovedad}, mensajePropio + PaginaCOMP.usuario.mensajes[i].previewUltimoMensaje),
							]));
						} else {
							listaMensajes.push(m("a", {class: "collection-item waves-effect", href:"javascript:PaginaCOMP.cambiarUrl('/chat?id=" + PaginaCOMP.usuario.mensajes[i].id + "')"}, [
								m("img", {class: "circle avatar_mini left", src: "src/images/avatar.jpg"}, ""),
								m("div", {class: "lateral_list_persona"}, PaginaCOMP.usuario.mensajes[i].otroParticipanteNombre),
								m("div", {class: "lateral_list_detalle" + claseNovedad}, mensajePropio + PaginaCOMP.usuario.mensajes[i].previewUltimoMensaje),
							]));
						}
						
						if( window.location.hash.search("#/chat") == 0 && parametroUrl('id') == PaginaCOMP.usuario.mensajes[i].id ) {
							ContenidoCOMP.actualizarChatRecurrenteNuevos();
						}
					}
					listaMensajes.push(m("a", {class: "collection-item waves-effect", href:"javascript:PaginaCOMP.cambiarUrl('/conversaciones')"}, [
						m("div", {class: "lateral_list_completo"}, [
							"Listado completo"
						]),
					]));
				}
				m.render(document.getElementById("lateralMensaje"), m("div", {class: "collection lateral_menu_list"}, listaMensajes));
			}
		}
	},
	loguearModal: function(nombre) {
		abrirModal(nombre);
    },
	registrarModal: function(nombre) {
		abrirModal(nombre);
		$('.datepicker').datepicker({ 
            firstDay: true, 
            format: 'dd-mm-yyyy',
			yearRange: 100,
			maxDate: new Date(),
            i18n: {
                months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                weekdays: ["Domingo","Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
                weekdaysShort: ["Dom","Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
                weekdaysAbbrev: ["D","L", "M", "M", "J", "V", "S"],
				cancel: 'Cancelar',
				done: 'Aceptar'
            }
        });
    },
	registrarModalTyc: function() {
		abrirModal("#modalCargando");
		
		var formData = new FormData;
		
		m.request({
			method: "POST",
			url: urlServicios + "deslogueado/getTyc",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			cerrarModal("#modalCargando");
			$("#textoRegistrarTyc").html(data.tyc);
			abrirModal("#modalRegistrarTyc");     
		})
		.catch(function(data) {
			cerrarModal("#modalCargando");
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	loguearAccion: function() {
		
		if(validarCampos("#modalLogin")) {
			cerrarModal("#modalLogin");
			abrirModal("#modalCargando");
			var formData = new FormData;
			formData.append("email",	$("#form_login_user").val());
			formData.append("pass",		$("#form_login_pass").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "deslogueado/loguear",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: data.errorMensaje, classes: 'rounded green'});
				var formData = new FormData;
				m.request({
					method: "POST",
					url: urlServicios + "logueado/checkear",
					body: formData,
					serialize: function(value) {return value}
				})
				.then(function(data) {
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
						$("#form_login_user").val("");
						$("#form_login_pass").val("");
						cerrarModal("#modalCargando");
						LateralCOMP.actualizarSiguiendoMensajes();
						PaginaCOMP.actualizarMensajes();
						PaginaCOMP.actualizarSiguiendo();
					}
				});
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalLogin");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	abrirConfiguracion: function() {
		abrirModal("#modalConfigurar");
    },
	desloguearAccion: function() {
		PaginaCOMP.usuario.logueado = -1;
		PaginaCOMP.usuario.siguiendo = null;
		PaginaCOMP.usuario.mensajes = null;
		clearTimeout(PaginaCOMP.actualizarSiguiendoSetTime);
		
		var formData = new FormData;
		
		m.request({
			method: "POST",
			url: urlServicios + "logueado/desloguear",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			M.toast({html: data.errorMensaje, classes: 'rounded green'});
			var formData = new FormData;
			m.request({
				method: "POST",
				url: urlServicios + "logueado/checkear",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				location.href=urlWeb;
			});
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	registrarAccion: function() {
		if(validarCampos("#modalRegistrar")) {
			if( $("#form_reg_pass2").val() != $("#form_reg_pass1").val() ) {
				M.toast({html: "El Password y el Repetir Password no coinciden", classes: 'rounded red'});
				$( "#form_reg_pass2" ).removeClass( "valid" );
				$( "#form_reg_pass2" ).addClass( "invalid" );
				return;
			}
			
			cerrarModal("#modalRegistrar");
			abrirModal("#modalCargando");
			var formData = new FormData;
			formData.append("nick",				$("#form_reg_nick").val());
			formData.append("nombre",			$("#form_reg_nombre").val());
			formData.append("email",			$("#form_reg_email").val());
			formData.append("pass",				$("#form_reg_pass1").val());
			var fechaList = $("#form_reg_fecha").val().split('-');
			formData.append("fechaNacimiento",	fechaList[2] + '-' + fechaList[1] + '-' + fechaList[0]);
			
			m.request({
				method: "POST",
				url: urlServicios + "deslogueado/registrar",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: data.errorMensaje, classes: 'rounded green'});
				$("#form_reg_nick").val("");
				$("#form_reg_nombre").val("");
				$("#form_reg_email").val("");
				$("#form_reg_pass1").val("");
				$("#form_reg_pass2").val("");
				$("#form_reg_fecha").val("");
				cerrarModal("#modalCargando");
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalRegistrar");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	passwordAccion: function() {
		if(validarCampos("#modalPassword")) {
			cerrarModal("#modalPassword");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("email", $("#form_password_user").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "deslogueado/solicitarBlanqueoPassword",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: data.errorMensaje, classes: 'rounded green'});
				$("#form_password_user").val("");
				cerrarModal("#modalCargando");
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalPassword");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	abrirModalImagene: function(url) {
		$("#modalMostrarImagenImg").attr("src",url);
		abrirModal("#modalMostrarImagen");
    },
	abrirYCerrarModal: function(abrir, cerrar) {
		cerrarModal(cerrar);
		abrirModal(abrir);
    },
	configurarCambiarEmail: function() {
		if(validarCampos("#modalConfigurarEmail")) {
			cerrarModal("#modalConfigurarEmail");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("nuevoEmail", $("#form_con_cambiar_email").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "logueado/cambiarEmail",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha enviado un email a " + $("#form_con_cambiar_email").val() + " para completar la solicitud.", classes: 'rounded green'});
				$("#form_con_cambiar_email").val("");
				cerrarModal("#modalCargando");
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalConfigurarEmail");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	configurarCambiarNombre: function() {
		if(validarCampos("#modalConfigurarNombre")) {
			cerrarModal("#modalConfigurarNombre");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("nombre", $("#form_con_cambiar_nombre").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "logueado/setNombre",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				PaginaCOMP.usuario.nombre = $("#form_con_cambiar_nombre").val();
				M.toast({html: "Se ha modificado el nombre.", classes: 'rounded green'});
				$("#form_con_cambiar_nombre").val("");
				cerrarModal("#modalCargando");
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalConfigurarNombre");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	configurarCambiarPass: function() {
		if(validarCampos("#modalConfigurarPass")) {
			if( $("#form_con_cambiar_pass_1").val() != $("#form_con_cambiar_pass_2").val() ) {
				M.toast({html: "El Password nuevo y el Repetir Password no coinciden", classes: 'rounded red'});
				$( "#form_con_cambiar_pass_2" ).removeClass( "valid" );
				$( "#form_con_cambiar_pass_2" ).addClass( "invalid" );
				return;
			}
			
			cerrarModal("#modalConfigurarPass");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("claveAnterio", $("#form_con_cambiar_pass_ant").val());
			formData.append("claveNueva", $("#form_con_cambiar_pass_1").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "logueado/setClave",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha modificado el password.", classes: 'rounded green'});
				$("#form_con_cambiar_pass_ant").val("");
				$("#form_con_cambiar_pass_1").val("");
				$("#form_con_cambiar_pass_2").val("");
				cerrarModal("#modalCargando");
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalConfigurarPass");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	abrirAdministrarCambiarEmail: function() {
		cerrarModal("#modalDetalleUsuario");
		abrirModal("#modalDetalleUsuarioEmail");
		
		$("#form_det_usu_email").val("");
		$("#form_det_usu_email_ver").val("");
		$("#form_det_usu_email").removeClass( "valid" );
		$('div#modalDetalleUsuarioEmail select').formSelect();
    },
	abrirAdministrarCambiarPassword: function() {
		cerrarModal("#modalDetalleUsuario");
		abrirModal("#modalDetalleUsuarioPassword");
		
		$("#form_det_usu_password").val("");
		$("#form_det_usu_password").removeClass( "valid" );
    },
	abrirAdministrarCambiarBloqueado: function() {
		cerrarModal("#modalDetalleUsuario");
		abrirModal("#modalDetalleUsuarioBloqueado");
		
		$("#form_det_usu_bloqueado").val("");
		$('div#modalDetalleUsuarioBloqueado select').formSelect();
    },
	abrirAdministrarCambiarTipo: function() {
		if( ContenidoCOMP.usuarioBusqueda.tipoUsuario == 1 ) {
			M.toast({html: "No se puede modificar el tipo de usuario de un administrador", classes: 'rounded red'});
			return ;
		}
		cerrarModal("#modalDetalleUsuario");
		abrirModal("#modalDetalleUsuarioTipo");
		
		$("#form_det_usu_tipo").val("");
		$('div#modalDetalleUsuarioTipo select').formSelect();
    },
	administrarCambiarEmail: function() {
		if(validarCampos("#modalDetalleUsuarioEmail")) {
			cerrarModal("#modalDetalleUsuarioEmail");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("idUsuario", ContenidoCOMP.usuarioBusqueda.id);
			formData.append("nuevoEmail", $("#form_det_usu_email").val());
			formData.append("verificar", $("#form_det_usu_email_ver").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "administrador/recuperarCuentaEmail",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				if( $("#form_det_usu_email_ver").val() == 1 ) {
					M.toast({html: "Se ha enviado un email a " + $("#form_det_usu_email").val() + " para completar la solicitud.", classes: 'rounded green'});
				} else {
					M.toast({html: "Se ha modificado el email.", classes: 'rounded green'});
				}
				cerrarModal("#modalCargando");
				ContenidoCOMP.paginaUsuarioDetalle(ContenidoCOMP.usuarioBusqueda.id);
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalDetalleUsuarioEmail");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	administrarCambiarPassword: function() {
		if(validarCampos("#modalDetalleUsuarioPassword")) {
			cerrarModal("#modalDetalleUsuarioPassword");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("idUsuario", ContenidoCOMP.usuarioBusqueda.id);
			formData.append("nuevoPassword", $("#form_det_usu_password").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "administrador/recuperarCuentaPassword",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha modificado el password.", classes: 'rounded green'});
				cerrarModal("#modalCargando");
				ContenidoCOMP.paginaUsuarioDetalle(ContenidoCOMP.usuarioBusqueda.id);
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalDetalleUsuarioPassword");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	administrarCambiarBloqueado: function() {
		if(validarCampos("#modalDetalleUsuarioBloqueado")) {
			cerrarModal("#modalDetalleUsuarioBloqueado");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("idUsuario", ContenidoCOMP.usuarioBusqueda.id);
			formData.append("estaBloqueado", $("#form_det_usu_bloqueado").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "administrador/setEstaBloqueado",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha " + ($("#form_det_usu_bloqueado").val() == 0 ? "desbloqueado" : "bloqueado") + " el usuario.", classes: 'rounded green'});
				cerrarModal("#modalCargando");
				ContenidoCOMP.paginaUsuarioDetalle(ContenidoCOMP.usuarioBusqueda.id);
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalDetalleUsuarioBloqueado");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	administrarCambiarTipo: function() {
		if(validarCampos("#modalDetalleUsuarioTipo")) {
			cerrarModal("#modalDetalleUsuarioTipo");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("idUsuario", ContenidoCOMP.usuarioBusqueda.id);
			formData.append("tipoUsuario", $("#form_det_usu_tipo").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "administrador/setTipoUsuario",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha modificado el tipo de usuario.", classes: 'rounded green'});
				cerrarModal("#modalCargando");
				ContenidoCOMP.paginaUsuarioDetalle(ContenidoCOMP.usuarioBusqueda.id);
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalDetalleUsuarioTipo");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	administrarCambiarBloquadoPagina: function() {
		if(validarCampos("#modalDetalleUsuarioTipo")) {
			cerrarModal("#modalDetalleUsuarioTipo");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("idUsuario", ContenidoCOMP.usuarioBusqueda.id);
			formData.append("tipoUsuario", $("#form_det_usu_tipo").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "administrador/setTipoUsuario",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha modificado el tipo de usuario.", classes: 'rounded green'});
				cerrarModal("#modalCargando");
				ContenidoCOMP.paginaUsuarioDetalle(ContenidoCOMP.usuarioBusqueda.id);
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalDetalleUsuarioTipo");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	administrarCambiarBloqueadoPagina: function(lider, pagina, bloqueada) {
		if( bloqueada == 1 ) {
			bloqueada = 0;
		} else {
			bloqueada = 1;
		}
		
		abrirModal("#modalCargando");
			
		var formData = new FormData;
		formData.append("idUsuario", lider);
		formData.append("idPagina", pagina);
		formData.append("estaBloqueado", bloqueada);
		
		m.request({
			method: "POST",
			url: urlServicios + "administrador/setPaginaEstaBloqueado",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			M.toast({html: "Se ha " + (bloqueada == 1 ? "bloqueado" : "desbloqueado" ) + " la pagina.", classes: 'rounded green'});
			cerrarModal("#modalCargando");
			ContenidoCOMP.paginaUsuarioDetalle(ContenidoCOMP.usuarioBusqueda.id);
		})
		.catch(function(data) {
			cerrarModal("#modalCargando");
			abrirModal("#modalDetalleUsuarioTipo");
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	cargarImagenAvatar: function(e) {
		if( e.target.files.length > 0 ){
			cerrarModal("#modalConfigurar");
			abrirModal("#modalCargando");
			var formData = new FormData;
			formData.append("archivo",			e.target.files[0]);
			m.request({
				method: "POST",
				url: urlServicios + "logueado/cambiarImagen",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha modificado la imagen de avatar.", classes: 'rounded green'});
				PaginaCOMP.usuario.avatar = "../servicio/resources/image/perfiles/" + PaginaCOMP.usuario.idUsuario + "/" + data + "_med.jpg";
				cerrarModal("#modalCargando");
			}).catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalConfigurar");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
	},
	ejecutarCrearPagina: function() {
		if(validarCampos("#modalCrearPagina")) {
			cerrarModal("#modalCrearPagina");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("nombre", $("#form_con_crear_pag_nom").val());
			formData.append("descripcion", $("#form_con_crear_pag_des").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "pagina/crearPagina",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha creado la página.", classes: 'rounded green'});
				$("#form_con_crear_pag_nom").val("");
				$("#form_con_crear_pag_des").val("");
				PaginaCOMP.cambiarUrl('/lider?id=' + PaginaCOMP.usuario.idUsuario);
				cerrarModal("#modalCargando");
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalCrearPagina");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	ejecutarCrearPublicacion: function() {
		if(validarCampos("#modalCrearPublicacion")) {
			cerrarModal("#modalCrearPublicacion");
			abrirModal("#modalCargando");
			
			var formData = new FormData;
			formData.append("mensaje", $("#form_con_crear_pub").val());
			formData.append("idPagina", parametroUrl('id'));
			formData.append("cantidadImagenes", LateralCOMP.formCantidadImagenesArray.length);
			
			if( LateralCOMP.formCantidadImagenesArray.length > 0 ) {
				for (var i = 0; i < LateralCOMP.formCantidadImagenesArray.length; i++) {
					formData.append("archivo" + (i + 1), LateralCOMP.formCantidadImagenesArray[i].archivo );
				}
			}
			
			m.request({
				method: "POST",
				url: urlServicios + "pagina/crearPublicacion",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha creado la publicacion.", classes: 'rounded green'});
				PaginaCOMP.cambiarUrl('/siguiendo?id=' + parametroUrl('id') + '&lider=' + parametroUrl('lider'));
				cerrarModal("#modalCargando");
			})
			.catch(function(data) {
				cerrarModal("#modalCargando");
				abrirModal("#modalCrearPublicacion");
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	ejecutarModificarPublicacion: function() {
		if(validarCampos("#contenidoBodyDivModificar")) {
			
			var formData = new FormData;
			formData.append("idPagina", parametroUrl('pagina'));
			formData.append("idPublicacion", parametroUrl('id'));
			formData.append("mensaje", $("#form_mod_pub_tex").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "pagina/setPublicacion",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha modificado la publicacion.", classes: 'rounded green'});
				PaginaCOMP.cambiarUrl("/modificar?id=" + parametroUrl('id') + "&pagina=" + parametroUrl('pagina'));
			})
			.catch(function(data) {
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	ejecutarModificarPagina: function() {
		if(validarCampos("#contenidoBodyDivPagina")) {
			
			var formData = new FormData;
			formData.append("idPagina", parametroUrl('id'));
			formData.append("nombre", $("#form_mod_pag_nom").val());
			formData.append("descripcion", $("#form_mod_pag_des").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "pagina/setPagina",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha modificado la página.", classes: 'rounded green'});
				PaginaCOMP.cambiarUrl("/siguiendo?id=" + parametroUrl('id') + "&lider=" + PaginaCOMP.usuario.idUsuario);
			})
			.catch(function(data) {
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	ejecutarModificarBiografia: function() {
		if(validarCampos("#contenidoBodyDivBiografia")) {
			var formData = new FormData;
			formData.append("biografia", $("#form_mod_bio").val());
			
			m.request({
				method: "POST",
				url: urlServicios + "lider/setBiografia",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha modificado la biografía.", classes: 'rounded green'});
				PaginaCOMP.cambiarUrl("/lider?id=" + PaginaCOMP.usuario.idUsuario);
			})
			.catch(function(data) {
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	ejecutarBorrarPublicacion: function() {
		var formData = new FormData;
		formData.append("idPagina", parametroUrl('pagina'));
		formData.append("idPublicacion", parametroUrl('id'));
		
		m.request({
			method: "POST",
			url: urlServicios + "pagina/eliminarPublicacion",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			M.toast({html: "Se ha borrado la publicacion.", classes: 'rounded green'});
			PaginaCOMP.cambiarUrl("/siguiendo?id=" + parametroUrl('pagina') + "&lider=" + PaginaCOMP.usuario.idUsuario);
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	ejecutarBorrarPagina: function() {
		var formData = new FormData;
		formData.append("idPagina", parametroUrl('id'));
		
		m.request({
			method: "POST",
			url: urlServicios + "pagina/eliminarPagina",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			M.toast({html: "Se ha borrado la página.", classes: 'rounded green'});
			PaginaCOMP.cambiarUrl("/lider?id=" + PaginaCOMP.usuario.idUsuario);
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	ejecutarModificarTyc: function() {
		var formData = new FormData;
		formData.append("texto", $('#form_mod_tyc').val());
		
		m.request({
			method: "POST",
			url: urlServicios + "administrador/setTyc",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			M.toast({html: "Se ha modificado los términos y condiciones.", classes: 'rounded green'});
			cerrarModal('#modalModificarTyc');
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	abrirModalCrearPagina: function() {
		abrirModal("#modalCrearPagina");
    },
	abrirModalCrearPublicacion: function() {
		LateralCOMP.formCantidadImagenesHtml = [];
		LateralCOMP.formCantidadImagenesArray = [];
		m.redraw();
		$("#form_con_crear_pub").val("");
		abrirModal("#modalCrearPublicacion");
    },
	ejecutarCrearPublicacionAgregarImagen: function() {
		$("#agregar-imagen-miniatura").click();
    },
	ejecutarModificarPublicacionAgregarImagen: function() {
		$("#modificar-imagen-miniatura").click();
    },
	abrirModalCrearPublicacionChangeFile: function(event) {
		if( event.target.files.length > 0 ){
			var botonImagen = {};
			botonImagen.archivo = event.target.files[0];
			botonImagen.url = URL.createObjectURL(event.target.files[0]);
			LateralCOMP.formCantidadImagenesArray.push(botonImagen);
			document.getElementById('agregar-imagen-miniatura').type = "text";
			document.getElementById('agregar-imagen-miniatura').type = "file";
			LateralCOMP.listaBotonesImagenesPublicacionRefresh();
		}
    },
	abrirModalModificarPublicacionChangeFile: function(event) {
		if( event.target.files.length > 0 ){
			event.target.files[0];
			
			var formData = new FormData;
			formData.append("idPublicacion", parametroUrl('id'));
			formData.append("archivo", event.target.files[0]);
			
			m.request({
				method: "POST",
				url: urlServicios + "pagina/setPublicacionAddImagen",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				M.toast({html: "Se ha agregado la imagen.", classes: 'rounded green'});
				PaginaCOMP.cambiarUrl("/modificar?id=" + parametroUrl('id') + "&pagina=" + parametroUrl('pagina'));
			})
			.catch(function(data) {
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
			});
		}
    },
	listaBotonesImagenesPublicacionRefresh: function() {
		LateralCOMP.formCantidadImagenesHtml = [];
		for (var i = 0; i < LateralCOMP.formCantidadImagenesArray.length; i++) {
			LateralCOMP.formCantidadImagenesHtml.push(m("div", {class: "agregar-imagen-miniatura left"}, [
				m("a", {class: "btn agregar-imagen-miniatura-del", href:"javascript:LateralCOMP.listaBotonesImagenesPublicacionBorrar(" + i + ");"},[
					m("i", {class: "material-icons"}, "delete_forever")
				]),
				m("img", {class: "agregar-imagen-miniatura", src: LateralCOMP.formCantidadImagenesArray[i].url}, "")
			]));
		}
    },
	listaBotonesImagenesPublicacionBorrar: function(indice) {
		LateralCOMP.formCantidadImagenesArray.splice(indice, 1);
		LateralCOMP.listaBotonesImagenesPublicacionRefresh();
		m.redraw();
    },
	listaBotonesImagenesModificarBorrar: function(nombre) {
		var formData = new FormData;
		formData.append("idPublicacion", parametroUrl('id'));
		formData.append("imagenNombre", nombre);
		
		m.request({
			method: "POST",
			url: urlServicios + "pagina/setPublicacionRemoveImagen",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			M.toast({html: "Se borrado la imagen.", classes: 'rounded green'});
			PaginaCOMP.cambiarUrl("/modificar?id=" + parametroUrl('id') + "&pagina=" + parametroUrl('pagina'));
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	abrirModificarTyc: function() {
		abrirModal('#modalCargando');
		var formData = new FormData;
		
		m.request({
			method: "POST",
			url: urlServicios + "deslogueado/getTyc",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			cerrarModal('#modalCargando');
			abrirModal('#modalModificarTyc');
			$('#form_mod_tyc_con').html("<textarea id='form_mod_tyc' class='materialize-textarea validate requerido textarea-mod-tyc'></textarea>");
			$('#form_mod_tyc').val(data.tyc);
			M.textareaAutoResize($('#form_mod_tyc'));
		})
		.catch(function(data) {
			cerrarModal('#modalCargando');
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
    oncreate: function(vnode) {
        $('.collapsible').collapsible({
			accordion: false
		});
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
		return m("div", {class: "menu_lateria left"},[
			m("div", {style: PaginaCOMP.usuario.logueado == -1 ? "" : "display: none"}, [
				m("img", {class: "cargando", src: urlCargando} , "")
			]),
			m("div", {style: PaginaCOMP.usuario.logueado == -0 ? "" : "display: none"}, [
				m("img", {class: "circle avatar", src: PaginaCOMP.usuario.avatar} , ""),
				m("div", {class: "label_nombre"} , PaginaCOMP.usuario.nombre),
				m("hr", ""),
				m("a", {class: "menu_login waves-effect waves-light btn black", href:"javascript:LateralCOMP.loguearModal('#modalLogin');"},[
					m("i", {class: "material-icons large left"}, "person"),"Loguear"
				]),
				m("a", {class: "menu_login waves-effect waves-light btn black", href:"javascript:LateralCOMP.registrarModalTyc();"},[
					m("i", {class: "material-icons large left"}, "border_color"),"Registrar"
				]),
			]),
			m("div", {style: PaginaCOMP.usuario.logueado == 1 ? "" : "display: none"}, [
				m("img", {class: "circle avatar", src: PaginaCOMP.usuario.avatar} , ""),
				m("a", {class: "boto_flat_circle dropdown-trigger btn-floating waves-effect waves-light btn black left", id: "bot_lat_logueado", href: "javascript:abrirMenu('#bot_lat_logueado');", "data-target": "men_lat_logueado"},[
					m("i", {class: "large material-icons"}, "menu"),
				]),
				
				m("ul", {id: "men_lat_logueado", class: "dropdown-content"} , [
					m("li", m("a", {href:"javascript:LateralCOMP.desloguearAccion();"}, [
						m("i", {class: "material-icons"}, "power_settings_new"), "Desloguear"
					])),
					m("li", m("a", {href:"javascript:LateralCOMP.abrirConfiguracion();"}, [
						m("i", {class: "material-icons"}, "settings"), "Configurar"
					])),
					m("li", {class: "divider", tabindex: "-1"}, ""),
					m("li", m("a", {href: "#!"}, [
						m("i", {class: "material-icons"}, "close"), "Cerrar"
					])),
				]),
				
				m("div", {class: "label_nombre"} , PaginaCOMP.usuario.nombre),
				m("hr", ""),
				m("ul", {class: "collapsible"}, [
					m("li", {class: "active"}, [
						m("div", {class: "collapsible-header"}, [m("i", {class: "material-icons"} , "account_circle"), "Siguiendo"]),
						m("div", {class: "collapsible-body"}, m("div#lateralSiguiendo", "")),
					]),
					m("li", {class: "active"}, [
						m("div", {class: "collapsible-header"}, [m("i", {class: "material-icons"} , "chat"), "Mensajes"]),
						m("div", {class: "collapsible-body"}, m("div#lateralMensaje", "")),
					]),
				]),
			]),
			
			// MODALS
			m("div", {id: "modalLogin", class: "modal"}, [
				m("div", {class: "modal-content"},[
					m("h4", [
						"Loguear",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "mail_outline"),
								m("input", {id: "form_login_user", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_login_user"}, "Email *"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "settings_ethernet"),
								m("input", {id: "form_login_pass", type:"password", class:"validate requerido"}, ""),
								m("label", {for: "form_login_pass"}, "Password *"),
							]),
						]),
					]),
				]),
				m("div", {class: "modal-footer"},[
					m("a", {class: "modal-close waves-effect btn-flat modal_boton left", href:"javascript:LateralCOMP.registrarModal('#modalPassword');"}, "Recuperar contraseña"),
					m("a", {class: "modal-close waves-effect btn-flat modal_boton", href:"javascript:LateralCOMP.registrarModalTyc();"}, "Registrar"),
					m("a", {class: "waves-effect waves-green btn-flat modal_boton", href:"javascript:LateralCOMP.loguearAccion();"}, "Loguear"),
				]),
			]),
			
			m("div", {id: "modalRegistrar", class: "modal"},[
				m("div", {class: "modal-content"},[
					m("h4", [
						"Registrar",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "mail_outline"),
								m("input", {id: "form_reg_email", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_reg_email"}, "Email *"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "account_box"),
								m("input", {id: "form_reg_nick", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_reg_nick"}, "Nick *"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "assignment"),
								m("input", {id: "form_reg_nombre", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_reg_nombre"}, "Nombre *"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "cake"),
								m("input", {id: "form_reg_fecha", type:"text", class:"validate datepicker requerido"}, ""),
								m("label", {for: "form_reg_fecha"}, "Fecha Nacimiento *"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "settings_ethernet"),
								m("input", {id: "form_reg_pass1", type:"password", class:"validate requerido"}, ""),
								m("label", {for: "form_reg_pass1"}, "Password *"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "settings_ethernet"),
								m("input", {id: "form_reg_pass2", type:"password", class:"validate requerido"}, ""),
								m("label", {for: "form_reg_pass2"}, "Repetir Password *"),
							]),
						]),
					]),
				]),
				m("div", {class: "modal-footer"},[
					m("a", {class: "modal-close waves-effect btn-flat modal_boton", href:"javascript:LateralCOMP.loguearModal('#modalLogin');"}, "Loguear"),
					m("a", {class: "waves-effect waves-green btn-flat modal_boton", href:"javascript:LateralCOMP.registrarAccion();"}, "Registrar"),
				]),
			]),
			
			m("div", {id: "modalRegistrarTyc", class: "modal"},[
				m("div", {class: "modal-content"},[
					m("h4", [
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div#textoRegistrarTyc", ""),
						]),
					]),
				]),
				m("div", {class: "modal-footer"},[
					m("a", {class: "modal-close waves-effect btn-flat modal_boton", href:"javascript:cerrarModal('#modalRegistrarTyc');"}, "Rechazar"),
					m("a", {class: "waves-effect waves-green btn-flat modal_boton", href:"javascript:cerrarModal('#modalRegistrarTyc'); LateralCOMP.registrarModal('#modalRegistrar');"}, "Aceptar"),
				]),
			]),
			
			m("div", {id: "modalPassword", class: "modal"}, [
				m("div", {class: "modal-content"},[
					m("h4", [
						"Recuperar contraseña",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col m12"},[
								m("i", {class: "material-icons prefix"}, "mail_outline"),
								m("input", {id: "form_password_user", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_password_user"}, "Email *"),
							]),
						]),
					]),
				]),
				m("div", {class: "modal-footer"},[
					m("a", {class: "modal-close waves-effect btn-flat modal_boton left", href:"javascript:LateralCOMP.registrarModal('#modalLogin');"}, "Loguear"),
					m("a", {class: "waves-effect waves-green btn-flat modal_boton", href:"javascript:LateralCOMP.passwordAccion();"}, "Solicitar"),
				]),
			]),
			
			m("div", {id: "modalCargando", class: "modal modalCargando"},[
				m("div", {class: "modal-content"},[
					m("img", {class: "cargando", src: urlCargando} , ""),
				]),
			]),
			
			m("div", {id: "modalMostrarImagen", class: "modal"},[
				m("div", {class: "modal-content"},[
					m("h4", [
						"",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						//m("div", {class: "row"},[
						//	m("div", {class: "input-field col m12"},[
								m("img", {id: "modalMostrarImagenImg", src: ""}, ""),
						//	]),
						//]),
					]),
				]),
			]),
			
			m("div", {id: "modalConfigurar", class: "modal"}, [
				m("div", {class: "modal-content"},[
					m("h4", [
						"Configurar",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "mail_outline"),
								m("div", {class: "modal-configurar-label"}, "Email: " + PaginaCOMP.usuario.email),
							]),
							m("div", {class: "input-field col s12 m6 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirYCerrarModal('#modalConfigurarEmail', '#modalConfigurar');"}, "Modificar"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "account_box"),
								m("div", {class: "modal-configurar-label"}, "Nombre: " + PaginaCOMP.usuario.nombre),
							]),
							m("div", {class: "input-field col s12 m6 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirYCerrarModal('#modalConfigurarNombre', '#modalConfigurar');"}, "Modificar"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "settings_ethernet"),
								m("div", {class: "modal-configurar-label"}, "Password: **********"),
							]),
							m("div", {class: "input-field col s12 m6 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirYCerrarModal('#modalConfigurarPass', '#modalConfigurar');"}, "Modificar"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "sentiment_very_satisfied"),
								m("div", {class: "modal-configurar-label"}, "Avatar"),
							]),
							m("input[type=file]", {id: "modal-configurar-imagen", style: "display: none", onchange: LateralCOMP.cargarImagenAvatar, accept:".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"}, ""),
							m("div", {class: "input-field col s12 m6 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:$('#modal-configurar-imagen').click();"}, "Modificar"),
							]),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalConfigurarEmail", class: "modal"}, [
				m("div", {class: "modal-content"},[
					m("h4", [
						"Configurar - Email",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "mail_outline"),
								m("div", {class: "modal-configurar-label"}, "Email anterior: " + PaginaCOMP.usuario.email),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "mail_outline"),
								m("input", {id: "form_con_cambiar_email", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_con_cambiar_email"}, "Email nuevo *"),
							]),
							m("div", {class: "input-field col s12 m12 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.configurarCambiarEmail();"}, "Modificar"),
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirYCerrarModal('#modalConfigurar', '#modalConfigurarEmail');"}, "Volver"),
							]),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalConfigurarNombre", class: "modal"}, [
				m("div", {class: "modal-content"},[
					m("h4", [
						"Configurar - Nombre",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "account_box"),
								m("div", {class: "modal-configurar-label"}, "Nombre anterior: " + PaginaCOMP.usuario.nombre),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "account_box"),
								m("input", {id: "form_con_cambiar_nombre", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_con_cambiar_nombre"}, "Nombre nuevo *"),
							]),
							m("div", {class: "input-field col s12 m12 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.configurarCambiarNombre();"}, "Modificar"),
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirYCerrarModal('#modalConfigurar', '#modalConfigurarNombre');"}, "Volver"),
							]),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalConfigurarPass", class: "modal"}, [
				m("div", {class: "modal-content"},[
					m("h4", [
						"Configurar - Password",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m12"},[
								m("i", {class: "material-icons prefix"}, "settings_ethernet"),
								m("input", {id: "form_con_cambiar_pass_ant", type:"password", class:"validate requerido"}, ""),
								m("label", {for: "form_con_cambiar_pass_ant"}, "Password anterior *"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "settings_ethernet"),
								m("input", {id: "form_con_cambiar_pass_1", type:"password", class:"validate requerido"}, ""),
								m("label", {for: "form_con_cambiar_pass_1"}, "Password nuevo *"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "settings_ethernet"),
								m("input", {id: "form_con_cambiar_pass_2", type:"password", class:"validate requerido"}, ""),
								m("label", {for: "form_con_cambiar_pass_2"}, "Repetir password nuevo *"),
							]),
							m("div", {class: "input-field col s12 m12 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.configurarCambiarPass();"}, "Modificar"),
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirYCerrarModal('#modalConfigurar', '#modalConfigurarPass');"}, "Volver"),
							]),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalCrearPagina", class: "modal"},[
				m("div", {class: "modal-content"},[
					m("h4", [
						"Crear página",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "web"),
								m("input", {id: "form_con_crear_pag_nom", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_con_crear_pag_nom"}, "Nombre *"),
							]),
							m("div", {class: "input-field col s12 m12"},[
								m("i", {class: "material-icons prefix"}, "chrome_reader_mode"),
								m("input", {id: "form_con_crear_pag_des", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_con_crear_pag_des"}, "Descripcion *"),
							]),
							m("div", {class: "input-field col s12 m12 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.ejecutarCrearPagina();"}, "Crear"),
								m("a", {class: "btn", href:"javascript:cerrarModal('#modalCrearPagina');"}, "Volver"),
							]),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalCrearPublicacion", class: "modal"},[
				m("div", {class: "modal-content"},[
					m("h4", [
						"Crear publicacion",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m12"},[
								m("i", {class: "material-icons prefix"}, "chrome_reader_mode"),
								m("input", {id: "form_con_crear_pub", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_con_crear_pub"}, "Publicacion *"),
							]),
							m("div", {class: "input-field col s12 m12"},[
								LateralCOMP.formCantidadImagenesHtml,
								m("a", {class: "btn agregar-imagen-miniatura left", href:"javascript:LateralCOMP.ejecutarCrearPublicacionAgregarImagen();"}, "Agregar Imagen"),
								m("input[type=file]", {id: "agregar-imagen-miniatura", style: "display: none", onchange: LateralCOMP.abrirModalCrearPublicacionChangeFile, accept:".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"}, ""),
							]),
							m("div", {class: "input-field col s12 m12 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.ejecutarCrearPublicacion();"}, "Crear"),
								m("a", {class: "btn", href:"javascript:cerrarModal('#modalCrearPublicacion');"}, "Volver"),
							]),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalModificarTyc", class: "modal"},[
				m("div", {class: "modal-content"},[
					m("h4", [
						"Modificar términos y condiciones",
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div#form_mod_tyc_con", {class: "input-field col s12 m12"},""),
							m("div", {class: "input-field col s12 m12 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.ejecutarModificarTyc();"}, "Guardar"),
								m("a", {class: "btn", href:"javascript:cerrarModal('#modalModificarTyc');"}, "Cerrar"),
							]),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalListaUsuarios", class: "modal"},[
				m("div", {class: "modal-content"},[
					m("h4", [
						"Busqueda de usuarios",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("table", {class: "striped"}, [
						m("thead", [
							m("tr", [
								m("th", "Nombre"),
								m("th", "Email"),
								m("th", "Nick"),
								m("th", "Tipo"),
							]),
						]),
						m("tbody#modalListaUsuariosRender",[]),
					]),
				]),
			]),
			
			m("div", {id: "modalDetalleUsuario", class: "modal"},[
				m("div", {class: "modal-content"},[
					m("h4", [
						"Detalle de usuario",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "fiber_pin"),
								m("div", {class: "modal-configurar-label"}, "ID: " + ContenidoCOMP.usuarioBusqueda.id),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "check"),
								m("div", {class: "modal-configurar-label"}, "Verificado: " + (ContenidoCOMP.usuarioBusqueda.estaVerificado == 0 ? 'No' : 'Si')),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "assignment"),
								m("div", {class: "modal-configurar-label"}, "Nombre: " + ContenidoCOMP.usuarioBusqueda.nombre),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "account_box"),
								m("div", {class: "modal-configurar-label"}, "Nick: " + ContenidoCOMP.usuarioBusqueda.nick),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "mail_outline"),
								m("div", {class: "modal-configurar-label"}, "Email: " + ContenidoCOMP.usuarioBusqueda.email),
							]),
							m("div", {class: "input-field col s12 m6 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirAdministrarCambiarEmail();"}, "Modificar"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "settings_ethernet"),
								m("div", {class: "modal-configurar-label"}, "Password: **********"),
							]),
							m("div", {class: "input-field col s12 m6 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirAdministrarCambiarPassword();"}, "Modificar"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "block"),
								m("div", {class: "modal-configurar-label"}, "Bloqueado: " + (ContenidoCOMP.usuarioBusqueda.estaBloqueado == 0 ? 'No' : 'Si')),
							]),
							m("div", {class: "input-field col s12 m6 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirAdministrarCambiarBloqueado();"}, "Modificar"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "accessibility"),
								m("div", {class: "modal-configurar-label"}, "Tipo de usuario: " + (ContenidoCOMP.usuarioBusqueda.tipoUsuario == 1 ? 'Administrador' : ContenidoCOMP.usuarioBusqueda.tipoUsuario == 2 ? 'Lider' : 'Deboto')),
							]),
							m("div", {class: "input-field col s12 m6 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirAdministrarCambiarTipo();"}, "Modificar"),
							]),
							
							m("div#listadoBusquedaUsuariosPaginas", ""),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalDetalleUsuarioEmail", class: "modal"}, [
				m("div", {class: "modal-content"},[
					m("h4", [
						"Administrador - Configurar - Email",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "mail_outline"),
								m("div", {class: "modal-configurar-label"}, "Email anterior: " + ContenidoCOMP.usuarioBusqueda.email),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "mail_outline"),
								m("input", {id: "form_det_usu_email", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_det_usu_email"}, "Email nuevo *"),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "check"),
								m("select", {id: "form_det_usu_email_ver", class:"validate requerido"}, [
									m("option", {value: ""}, ""),
									m("option", {value: "0"}, "No"),
									m("option", {value: "1"}, "Si"),
								]),
								m("label", "¿Necesita verificación? *"),
							]),
							m("div", {class: "input-field col s12 m12 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.administrarCambiarEmail();"}, "Modificar"),
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirYCerrarModal('#modalDetalleUsuario', '#modalDetalleUsuarioEmail');"}, "Volver"),
							]),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalDetalleUsuarioPassword", class: "modal"}, [
				m("div", {class: "modal-content"},[
					m("h4", [
						"Administrador - Configurar - Password",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "mail_outline"),
								m("input", {id: "form_det_usu_password", type:"text", class:"validate requerido"}, ""),
								m("label", {for: "form_det_usu_password"}, "Password nuevo *"),
							]),
							m("div", {class: "input-field col s12 m12 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.administrarCambiarPassword();"}, "Modificar"),
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirYCerrarModal('#modalDetalleUsuario', '#modalDetalleUsuarioPassword');"}, "Volver"),
							]),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalDetalleUsuarioBloqueado", class: "modal"}, [
				m("div", {class: "modal-content"},[
					m("h4", [
						"Administrador - Configurar - Bloqueado",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "block"),
								m("div", {class: "modal-configurar-label"}, "Bloqueado: " + (ContenidoCOMP.usuarioBusqueda.estaBloqueado == 0 ? 'No' : 'Si')),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "check"),
								m("select", {id: "form_det_usu_bloqueado", class:"validate requerido"}, [
									m("option", {value: ""}, ""),
									m("option", {value: "0", disabled: ContenidoCOMP.usuarioBusqueda.estaBloqueado == 0 ? "disabled" : ""}, "No"),
									m("option", {value: "1", disabled: ContenidoCOMP.usuarioBusqueda.estaBloqueado == 1 ? "disabled" : ""}, "Si"),
								]),
								m("label", "¿Cambiar el bloqueo a? *"),
							]),
							m("div", {class: "input-field col s12 m12 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.administrarCambiarBloqueado();"}, "Modificar"),
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirYCerrarModal('#modalDetalleUsuario', '#modalDetalleUsuarioBloqueado');"}, "Volver"),
							]),
						]),
					]),
				]),
			]),
			
			m("div", {id: "modalDetalleUsuarioTipo", class: "modal"}, [
				m("div", {class: "modal-content"},[
					m("h4", [
						"Administrador - Configurar - Tipo de usuario",
						m("a", {class: "modal-close waves-effect waves-red btn-flat right modal_cerrar"}, "X"),
					]),
					m("div", {class: "col s12"},[
						m("div", {class: "row"},[
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "accessibility"),
								m("div", {class: "modal-configurar-label"}, "Tipo de usuario: " + (ContenidoCOMP.usuarioBusqueda.tipoUsuario == 1 ? 'Administrador' : ContenidoCOMP.usuarioBusqueda.tipoUsuario == 2 ? 'Lider' : 'Deboto')),
							]),
							m("div", {class: "input-field col s12 m6"},[
								m("i", {class: "material-icons prefix"}, "check"),
								m("select", {id: "form_det_usu_tipo", class:"validate requerido"}, [
									m("option", {value: ""}, ""),
									m("option", {value: "2", disabled: ContenidoCOMP.usuarioBusqueda.tipoUsuario == 2 ? "disabled" : ""}, "Lider"),
									m("option", {value: "3", disabled: ContenidoCOMP.usuarioBusqueda.tipoUsuario == 3 ? "disabled" : ""}, "Deboto"),
								]),
								m("label", "¿Cambiar el tipo de usuario a? *"),
							]),
							m("div", {class: "input-field col s12 m12 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.administrarCambiarTipo();"}, "Modificar"),
								m("a", {class: "btn", href:"javascript:LateralCOMP.abrirYCerrarModal('#modalDetalleUsuario', '#modalDetalleUsuarioBloqueado');"}, "Volver"),
							]),
						]),
					]),
				]),
			]),
			
			
			
			
			
			
			
			
		]);
    }
}