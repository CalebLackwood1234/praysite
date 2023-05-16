var ContenidoCOMP = {
	tipo: "CAR",
	mesajes: {},
	novedades: {},
	lider: {},
	usuarioBusqueda: {},
	modificar: {},
	pagina: {},
    oninit: function(vnode) {
        this.tipo = "CAR";
		ContenidoCOMP.busqueda = {};
		ContenidoCOMP.busqueda.resultados = [];
		ContenidoCOMP.busqueda.pagina = [];
    },
	handleEnterMensajeEvento: function(event) {
		if (event.code === 'Enter' || event.code === 'NumpadEnter') {
			ContenidoCOMP.enviarMensajeAccion();
		}
	},
    actualizarUrl: function() {
		document.getElementById("contenidoBodyChatInputText").removeEventListener('keypress', ContenidoCOMP.handleEnterMensajeEvento);
        if( window.location.hash.search("#/chat") == 0 ) {
			ContenidoCOMP.tipo = "CAR";
			ContenidoCOMP.mesajes = {};
			
			if( parametroUrl('id') == null ) {
				var formData = new FormData;
				
				formData.append("lider",	parametroUrl('lider'));
				
				m.request({
					method: "POST",
					url: urlServicios + "mensaje/buscarChat",
					body: formData,
					serialize: function(value) {return value}
				})
				.then(function(data) {
					if( data != null && data.id != null ) {
						PaginaCOMP.cambiarUrl("/chat?id=" + data.id);
						PaginaCOMP.actualizarMensajes();
					} else {
						ContenidoCOMP.tipo = "CHA";
						document.getElementById("contenidoBodyChatInputText").addEventListener("keypress", ContenidoCOMP.handleEnterMensajeEvento);
						ContenidoCOMP.mesajes.listaMensajes = m("div", "No tienes mensajes");
					}
				})
				.catch(function(data) {
					M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
				});
			} else {
				var formData = new FormData;
				formData.append("idChat",	parametroUrl('id'));
				
				m.request({
					method: "POST",
					url: urlServicios + "mensaje/leerChat",
					body: formData,
					serialize: function(value) {return value}
				})
				.then(function(data) {
					ContenidoCOMP.tipo = "CHA";
					document.getElementById("contenidoBodyChatInputText").addEventListener("keypress", ContenidoCOMP.handleEnterMensajeEvento);

					if(data.length == 0) {
						ContenidoCOMP.mesajes.listaMensajes = m("div", "No tienes mensajes");
					} else {
						ContenidoCOMP.mesajes.avatarOtro = "src/images/avatar.jpg";
						if(data[0].urtImagen != null) {
							ContenidoCOMP.mesajes.avatarOtro = "../servicio/resources/image/perfiles/" + data[0].idOtro + "/" + data[0].urtImagen + "_mini.jpg";
						}
						
						ContenidoCOMP.mesajes.lista = [];
						ContenidoCOMP.mesajes.primero = data[0].id;
						ContenidoCOMP.mesajes.ultimo = data[data.length - 1].id;
						ContenidoCOMP.mesajes.otroId = data[0].idOtro;
						ContenidoCOMP.mesajes.listaMensajes = [];

						for (i = 0; i < data.length; i++) {
							ContenidoCOMP.mesajes.lista.splice(0, 0, data[i]);
							ContenidoCOMP.mesajes.listaMensajes.splice(0, 0, ContenidoCOMP.actualizarChat(data[i]));
						}
						ContenidoCOMP.mesajes.posicion = 0;
						ContenidoCOMP.mesajes.actualizado = 1;
					}
				})
				.catch(function(data) {
					m.render(document.getElementById("contenidoBody"), m("div", {class: "red"}, data.response.errorMensaje));
					M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
					ContenidoCOMP.tipo = "ERR";
				});
			}
		} else if( window.location.hash.search("#/siguiendo") == 0 ) {
			ContenidoCOMP.tipo = "CAR";
			ContenidoCOMP.novedades = {};
			ContenidoCOMP.novedades.propio = 0;
			if( PaginaCOMP.usuario.logueado == 1 && PaginaCOMP.usuario.idUsuario == parametroUrl('lider') ) {
				ContenidoCOMP.novedades.propio = 1;
			}
			
			var formData = new FormData;
			formData.append("idLider",	parametroUrl('lider'));
			formData.append("idPagina",	parametroUrl('id'));
			
			m.request({
				method: "POST",
				url: urlServicios + "seguir/listarPublicacionesPagina",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				ContenidoCOMP.tipo = "SIG";
				ContenidoCOMP.novedades.lista = [];
				ContenidoCOMP.novedades.usuarioNovedad = [];
				ContenidoCOMP.novedades.listaNovedad = [];
				
				
				if( ContenidoCOMP.novedades.propio == 1 ) {
					ContenidoCOMP.novedades.listaNovedad.push( m("div", {class: "card publicacion-card"}, [
						m("div", {class: "card-content"}, [
							m("a", {class: "lider-pagina", href:"javascript:LateralCOMP.abrirModalCrearPublicacion();"}, [
								m("i", {class: "material-icons left"}, "add"),
								m("div", "Nueva publicación"),
							]),
						]),
					]));
				}
				
				ContenidoCOMP.novedades.lider = parametroUrl('lider');
				ContenidoCOMP.novedades.siguiendo = data.siguiendo;
				ContenidoCOMP.novedades.usuarioNovedad = m("div", {class: "card publicacion-card lider-pagina"}, [
						m("div", {class: "lider-pagina-titulo"}, [
							m("span", {class: "text-blue"}, m("i", {class: "material-icons"}, "language")),
							m("span", {class: "text-blue"}, data.nombre),
							m("a#lider-pagina-dejarseguir", {class: "boton-megusta waves-effect waves-teal btn-flat grey lighten-4 right", style: ContenidoCOMP.novedades.propio == 0 && ContenidoCOMP.novedades.siguiendo == 1 ? "" : "display: none", href: "javascript:ContenidoCOMP.dejarSeguirAccion(" + data.id + "," + data.lider + ");"}, [
								m("span", {class: ""}, "Dejar de seguir"),
								m("i", {class: "material-icons"}, "clear"),
							]),
							m("a#lider-pagina-seguir", {class: "boton-megusta waves-effect waves-teal btn-flat grey lighten-4 right", style: ContenidoCOMP.novedades.propio == 0 && ContenidoCOMP.novedades.siguiendo == 0 ? "" : "display: none", href: "javascript:ContenidoCOMP.seguirAccion(" + data.id + "," + data.lider + ");"}, [
								m("span", {class: ""}, "Seguir"),
								m("i", {class: "material-icons"}, "add"),
							]),
							m("a", {class: "right", style: ContenidoCOMP.novedades.propio == 1 ? "" : "display: none", href: "javascript:PaginaCOMP.cambiarUrl('/pagina?id=" + parametroUrl('id') + "');"}, "Modificar"),
						]),
						m("div", data.descripcion),
				]);
				
				if(data.publicaciones.length == 0) {
					ContenidoCOMP.novedades.listaNovedad.push(m("div", "No hay publicaciones disponibles"));
				} else {
					ContenidoCOMP.novedades.primero = data.publicaciones[0].id;
					ContenidoCOMP.novedades.ultimo = data.publicaciones[data.publicaciones.length - 1].id;
					
					for (i = 0; i < data.publicaciones.length; i++) {
						ContenidoCOMP.novedades.lista.push(data.publicaciones[i]);
						ContenidoCOMP.novedades.listaNovedad.push(ContenidoCOMP.actualizarSiguiendo(data.publicaciones[i]));
					}
					ContenidoCOMP.novedades.posicion = 0;
					ContenidoCOMP.novedades.actualizado = 1;
				}
			})
			.catch(function(data) {
				m.render(document.getElementById("contenidoBody"), m("div", {class: "red"}, data.response.errorMensaje));
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
				ContenidoCOMP.tipo = "ERR";
			});
		} else if( window.location.hash.search("#/lider") == 0 ) {
			ContenidoCOMP.lider = {};
			ContenidoCOMP.lider.paginas = [];
			ContenidoCOMP.lider.avatar = "src/images/avatar.jpg";
			ContenidoCOMP.lider.enviarMensaje = 0;
			ContenidoCOMP.lider.propio = 0;
			if( PaginaCOMP.usuario.logueado == 1 && PaginaCOMP.usuario.idUsuario == parametroUrl('id') ) {
				ContenidoCOMP.lider.propio = 1;
			}
			
			var formData = new FormData;
			formData.append("idLider",	parametroUrl('id'));
			
			m.request({
				method: "POST",
				url: urlServicios + "seguir/listarPaginasLider",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				if( data.habilitado == 0 ) {
					M.toast({html: "Lider no encontrado", classes: 'rounded red'});
					ContenidoCOMP.tipo = "ERR";
					return "";
				}
				ContenidoCOMP.tipo = "LID";
				ContenidoCOMP.lider.biografia = data.biografia;
				ContenidoCOMP.lider.nombre = data.liderNombre;
				ContenidoCOMP.lider.nick = data.liderNick;
				
				var formData = new FormData;
				formData.append("idReceptor",	parametroUrl('id'));
				
				if( PaginaCOMP.usuario.logueado == 1 && ContenidoCOMP.lider.propio == 0 ) {
					m.request({
						method: "POST",
						url: urlServicios + "mensaje/puedeEscribir",
						body: formData,
						serialize: function(value) {return value}
					})
					.then(function(data) {
						ContenidoCOMP.lider.enviarMensaje = 1;
					})
					.catch(function(data) {});
				} else {
					$("#form_mod_bio_lab").addClass("active");
					$("#form_mod_bio").val(data.biografia);
				}
				
				if( data.liderUrlImagen != null && data.liderUrlImagen.length > 0 ) {
					ContenidoCOMP.lider.avatar = "../servicio/resources/image/perfiles/" + parametroUrl('id') + "/" + data.liderUrlImagen + ".jpg";
				}
				if( data.paginas != null && data.paginas.length > 0 ) {
					for (i = 0; i < data.paginas.length; i++) {
						if( data.paginas[i].bloqueada != 1 ) {
							var paginaHtml = null;
							if( data.paginas[i].habilitada == 1 ) {
								paginaHtml = m("a", {class: "lider-pagina", href:"javascript:PaginaCOMP.cambiarUrl('/siguiendo?id=" + data.paginas[i].id + "&lider=" + data.paginas[i].lider + "')"}, [
									m("i", {class: "material-icons left"}, "language"),
									m("div", data.paginas[i].nombre),
								]);
							} else {
								paginaHtml = m("div", {class: "lider-pagina"}, [
									m("i", {class: "material-icons left"}, "lock_outline"),
									m("div", data.paginas[i].nombre),
								]);
							}
							
							ContenidoCOMP.lider.paginas.push( m("div", {class: "card publicacion-card"}, [
								m("div", {class: "card-content"}, [
									paginaHtml,
									m("hr", ""),
									m("div", data.paginas[i].descripcion),
								]),
							]));
						}
					}
				}
				if( ContenidoCOMP.lider.propio == 1 ) {
					ContenidoCOMP.lider.paginas.push( m("div", {class: "card publicacion-card"}, [
						m("div", {class: "card-content"}, [
							m("a", {class: "lider-pagina", href:"javascript:LateralCOMP.abrirModalCrearPagina();"}, [
								m("i", {class: "material-icons left"}, "add"),
								m("div", "Crear nueva página"),
							]),
						]),
					]));
				}
			})
			.catch(function(data) {
				m.render(document.getElementById("contenidoBody"), m("div", {class: "red"}, data.response.errorMensaje));
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
				ContenidoCOMP.tipo = "ERR";
			});
		} else if( window.location.hash.search("#/busqueda") == 0 ) {
			ContenidoCOMP.tipo = "CAR";
			ContenidoCOMP.busqueda.resultados = [];
			ContenidoCOMP.busqueda.pagina = [];
			
			var formData = new FormData;
			formData.append("busqueda",	decodeURIComponent(parametroUrl('valor')).replaceAll("\"", ""));
			formData.append("pagina",	1);
			
			m.request({
				method: "POST",
				url: urlServicios + "seguir/listarLideres",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				ContenidoCOMP.tipo = "BUS";
				if( data == null ) {
					ContenidoCOMP.busqueda.pagina = m("h5", "No se encontraron resultados");
				} else {
					for (i = 0; i < data.length; i++) {
						avatarBusqueda = "src/images/avatar.jpg";
						if(data[i].urtImagen != null) {
							avatarBusqueda = "../servicio/resources/image/perfiles/" + data[i].id + "/" + data[i].urtImagen + "_med.jpg"
						}
						ContenidoCOMP.busqueda.resultados.push(data[i]);
						ContenidoCOMP.busqueda.pagina.push(m("div", {class: "card publicacion-card"}, [
							m("a", {class: "", href:"javascript:PaginaCOMP.cambiarUrl('/lider?id=" + data[i].id + "')"}, [
								m("div", {class: "card-title"}, [
									m("img", {class: "circle avatar_busqueda left", src: avatarBusqueda}, ""),
									data[i].nombre + " (" + data[i].nick + ")",
								]),
							]),
						]));
					}
				}
			})
			.catch(function(data) {
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
				ContenidoCOMP.tipo = "ERR";
			});
		} else if( window.location.hash.search("#/novedades") == 0 ) {
			ContenidoCOMP.tipo = "CAR";
			ContenidoCOMP.novedades = {};
			ContenidoCOMP.novedades.usuarioNovedad = [];
			ContenidoCOMP.novedades.listaNovedad = [];
			ContenidoCOMP.novedades.lista = [];
			
			var formData = new FormData;
			
			m.request({
				method: "POST",
				url: urlServicios + "seguir/muroPublico",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				ContenidoCOMP.tipo = "SIG";
				
				if(data.length == 0) {
					var listaSiguiendo = listaMensajes.push(m("div", "No hay publicaciones disponibles"));
					m.render(document.getElementById("contenidoBody"), m("div", listaSiguiendo));
				} else {
					
					ContenidoCOMP.novedades.primero = data[0].id;
					ContenidoCOMP.novedades.ultimo = data[data.length - 1].fechaModificacion;
					
					for (i = 0; i < data.length; i++) {
						ContenidoCOMP.novedades.lista.push(data[i]);
						ContenidoCOMP.novedades.listaNovedad.push(ContenidoCOMP.actualizarNovedad(data[i]));
					}
					ContenidoCOMP.novedades.posicion = 0;
					ContenidoCOMP.novedades.actualizado = 1;
				}
			})
			.catch(function(data) {
				m.render(document.getElementById("contenidoBody"), m("div", {class: "red"}, data.response.errorMensaje));
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
				ContenidoCOMP.tipo = "ERR";
			});
		} else if( window.location.hash.search("#/modificar") == 0 ) {
			ContenidoCOMP.tipo = "CAR";
			ContenidoCOMP.modificar.mensaje = "";
			ContenidoCOMP.modificarImagenes = [];
			
			var formData = new FormData;
			formData.append("idPagina", parametroUrl('pagina'));
			formData.append("idPublicacion", parametroUrl('id'));
			
			m.request({
				method: "POST",
				url: urlServicios + "pagina/getPublicacion",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				ContenidoCOMP.modificar = data;
				ContenidoCOMP.tipo = "MOD";
				$("#form_mod_pub_tex_lab").addClass("active");
				$("#form_mod_pub_tex").val(ContenidoCOMP.modificar.mensaje);
				if( data.nombreImagenes != null && data.nombreImagenes != "" ) {
					var listadoImagenes = data.nombreImagenes.split(";");
					for( var i = 1; i < listadoImagenes.length; i++ ) {
						ContenidoCOMP.modificarImagenes.push(m("div", {class: "agregar-imagen-miniatura left"}, [
							m("a", {class: "btn agregar-imagen-miniatura-del", href:"javascript:LateralCOMP.listaBotonesImagenesModificarBorrar('" + listadoImagenes[i] + "');"},[
								m("i", {class: "material-icons"}, "delete_forever")
							]),
							m("img", {class: "agregar-imagen-miniatura", src: "../servicio/resources/image/publicacion/" + PaginaCOMP.usuario.idUsuario + "/" + parametroUrl('pagina') + "/" + listadoImagenes[i] + ".jpg"}, "")
						]));
					}
				}
				
			})
			.catch(function(data) {
				m.render(document.getElementById("contenidoBody"), m("div", {class: "red"}, data.response.errorMensaje));
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
				ContenidoCOMP.tipo = "ERR";
			});
		} else if( window.location.hash.search("#/pagina") == 0 ) {
			ContenidoCOMP.tipo = "CAR";
			
			var formData = new FormData;
			formData.append("idPagina", parametroUrl('id'));
			
			m.request({
				method: "POST",
				url: urlServicios + "pagina/getPagina",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				console.log(data);
				ContenidoCOMP.tipo = "PAG";
				ContenidoCOMP.pagina = data;
				$("#form_mod_pag_nom_lab").addClass("active");
				$("#form_mod_pag_nom").val(data.nombre);
				$("#form_mod_pag_des_lab").addClass("active");
				$("#form_mod_pag_des").val(data.descripcion);
			})
			.catch(function(data) {
				m.render(document.getElementById("contenidoBody"), m("div", {class: "red"}, data.response.errorMensaje));
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
				ContenidoCOMP.tipo = "ERR";
			});
		} else if( window.location.hash.search("#/admin") == 0 ) {
			ContenidoCOMP.tipo = "ADM";
			$("#form_bus_usu_pag_lab").addClass("active");
			$("#form_bus_usu_pag").val("1");
			$('div#contenidoBodyDivAdmin select').formSelect();
			
			$('div#contenidoBodyDivAdmin .datepicker').datepicker({ 
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
		} else if( window.location.hash.search("#/conversaciones") == 0 ) {
			ContenidoCOMP.tipo = "CAR";
			ContenidoCOMP.chatTodos = [];

			var formData = new FormData;
			
			m.request({
				method: "POST",
				url: urlServicios + "mensaje/listarChatsTodos",
				body: formData,
				serialize: function(value) {return value}
			})
			.then(function(data) {
				ContenidoCOMP.tipo = "CHT";
				
				if(data.length == 0) {
					ContenidoCOMP.chatTodos.push(m("div", "No hay conversaciones disponibles"));
				} else {
					for (i = 0; i < data.length; i++) {
						console.log(data[i]);
						var autorMensaje = "";
						if( data[i].previewUltimoMensajeUsuarioId != data[i].otroParticipante ) {
							autorMensaje = "Tu: ";
						}
						
						var imagenPreview = "src/images/avatar.jpg";
						if( data[i].otroParticipanteUrlImagen != null ) {
							imagenPreview = "../servicio/resources/image/perfiles/" + data[i].otroParticipante + "/" + data[i].otroParticipanteUrlImagen + "_mini.jpg";
						}
						
						ContenidoCOMP.chatTodos.push( m("a", {href:"javascript:PaginaCOMP.cambiarUrl('/chat?id=" + data[i].id + "')"}, [
							m("div", {class: "card publicacion-card waves-effect lista_conversaciones"}, [
								m("img", {class: "circle avatar_mini left", src: imagenPreview}, ""),
								data[i].otroParticipanteNombre,
								m("hr", ""),
								m("span", {style: data[i].fueVistoPorMi == 0 ? "font-weight: bold;" : "" }, (data[i].fueVistoPorMi == 0 ? "* " : "") + autorMensaje + data[i].previewUltimoMensaje),
							])
						]));
					}
				}
			})
			.catch(function(data) {
				m.render(document.getElementById("contenidoBody"), m("div", {class: "red"}, data.response.errorMensaje));
				M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
				ContenidoCOMP.tipo = "ERR";
			});
		}
    },
	actualizarNovedad: function(data) {
		var galeriaImagenes = [];
		if(data.nombreImagenes != "") {
			var galeriaImagenesPush = [];
			var imagenesSeparadas = data.nombreImagenes.split(';');
			for (j = 1; j < imagenesSeparadas.length; j++) {
				galeriaImagenesPush.push(
					m("li",
						m("a", {href:"javascript:LateralCOMP.abrirModalImagene('../servicio/resources/image/publicacion/" + data.idLider + "/" + data.idPagina + "/" + imagenesSeparadas[j] + ".jpg')"},
							m("img", {class: "", src: "../servicio/resources/image/publicacion/" + data.idLider + "/" + data.idPagina + "/" + imagenesSeparadas[j] + ".jpg"}, ""),
						),
					),
				);
			}
			galeriaImagenes.push(m("div.slider", m("ul.slides", galeriaImagenesPush)));
		}
		return m("div", {class: "card publicacion-card"}, [
			m("div", {class: "titulo-usuario-publicacion"}, [
				m("img", {class: "circle avatar_mini left", src: data.imagenLider == "" || data.imagenLider == null ? "src/images/avatar.jpg" : "../servicio/resources/image/perfiles/" + data.idLider + "/" + data.imagenLider + "_mini.jpg"}, ""),
				m("a", {href: "javascript:PaginaCOMP.cambiarUrl('/lider?id=" + data.idLider + "')"}, [
					m("span", data.nombrePagina + " - " + data.nombreLider),
					m("span.titulo-usuario-publicacion-nick", " (" + data.nickLider + ")"),
				]),
			]),
			m("div.galeriaImagenes", galeriaImagenes),
			m("div", {class: ""}, data.mensaje),
			m("div", {class: "right-align"}, [
				m("a", {class: "boton-megusta waves-effect waves-teal btn-flat grey lighten-4", id: "bot_megusta_" + data.idPublicacion, style: data.dioMegusta == 1 ? "display: none" : "", href: "javascript:ContenidoCOMP.meGustaAccion(" + data.idPublicacion + "," + data.idPagina + "," + data.idLider + ");"}, [
					m("span", {class: ""}, "Me gusta"),
					m("i", {class: "material-icons"}, "thumb_up"),
				]),
				m("a", {class: "boton-megusta waves-effect waves-teal btn-flat grey lighten-4", id: "bot_nomegusta_" + data.idPublicacion, style: data.dioMegusta == 0 ? "display: none" : "", href: "javascript:ContenidoCOMP.nomeGustaAccion(" + data.idPublicacion + "," + data.idPagina + "," + data.idLider + ");"}, [
					m("span", {class: ""}, "No me gusta"),
					m("i", {class: "material-icons"}, "thumb_down"),
				]),
				m("div", {class: "texto-megusta", style: data.dioMegusta == 0 ? "display: none" : "", id: "text_nomegusta_" + data.idPublicacion}, "Cantidad de me gustan " + (data.meGusta - data.dioMegusta + 1)),
				m("div", {class: "texto-megusta", style: data.dioMegusta == 1 ? "display: none" : "", id: "text_megusta_" + data.idPublicacion}, "Cantidad de me gustan " + (data.meGusta - data.dioMegusta)),
			]),
		]);
	},
	actualizarSiguiendo: function(data) {
		var galeriaImagenes = [];
		if(data.nombreImagenes != "") {
			var galeriaImagenesPush = [];
			var imagenesSeparadas = data.nombreImagenes.split(';');
			for (j = 1; j < imagenesSeparadas.length; j++) {
				galeriaImagenesPush.push(
					m("li",
						m("a", {href:"javascript:LateralCOMP.abrirModalImagene('" + "../servicio/resources/image/publicacion/" + data.lider + "/" + data.pagina + "/" + imagenesSeparadas[j] + ".jpg')"},
							m("img", {class: "", src: "../servicio/resources/image/publicacion/" + data.lider + "/" + data.pagina + "/" + imagenesSeparadas[j] + ".jpg"}, ""),
						),
					),
				);
			}
			galeriaImagenes.push(m("div.slider", m("ul.slides", galeriaImagenesPush)));
		}
		return m("div", {class: "card publicacion-card"}, [
			m("div", {class: "titulo-usuario-publicacion"}, [
				m("img", {class: "circle avatar_mini left", src: data.urtImagen == "" || data.urtImagen == null ? "src/images/avatar.jpg" : "../servicio/resources/image/perfiles/" + data.lider + "/" + data.urtImagen + "_mini.jpg"}, ""),
				m("a", {href: "javascript:PaginaCOMP.cambiarUrl('/lider?id=" + data.lider + "')"}, [
					m("span", data.nombrePagina + " - " + data.nombre),
					m("span.titulo-usuario-publicacion-nick", " (" + data.nick + ")"),
				]),
				m("a", {class: "right", style: ContenidoCOMP.novedades.propio == 1 ? "" : "display: none", href: "javascript:PaginaCOMP.cambiarUrl('/modificar?id=" + data.id + "&pagina=" + data.pagina + "');"}, "Modificar"),
			]),
			m("div.galeriaImagenes", galeriaImagenes),
			m("div", {class: ""}, data.mensaje),
			m("div", {class: "right-align"}, [
				m("a", {class: "boton-megusta waves-effect waves-teal btn-flat grey lighten-4", id: "bot_megusta_" + data.id, style: data.dioMegusta == 1 ? "display: none" : "", href: "javascript:ContenidoCOMP.meGustaAccion(" + data.id + "," + data.pagina + "," + data.lider + ");"}, [
					m("span", {class: ""}, "Me gusta"),
					m("i", {class: "material-icons"}, "thumb_up"),
				]),
				m("a", {class: "boton-megusta waves-effect waves-teal btn-flat grey lighten-4", id: "bot_nomegusta_" + data.id, style: data.dioMegusta == 0 ? "display: none" : "", href: "javascript:ContenidoCOMP.nomeGustaAccion(" + data.id + "," + data.pagina + "," + data.lider + ");"}, [
					m("span", {class: ""}, "No me gusta"),
					m("i", {class: "material-icons"}, "thumb_down"),
				]),
				m("div", {class: "texto-megusta", style: data.dioMegusta == 0 ? "display: none" : "", id: "text_nomegusta_" + data.id}, "Cantidad de me gustan " + (data.meGusta - data.dioMegusta + 1)),
				m("div", {class: "texto-megusta", style: data.dioMegusta == 1 ? "display: none" : "", id: "text_megusta_" + data.id}, "Cantidad de me gustan " + (data.meGusta - data.dioMegusta)),
			]),
		]);
	},
	actualizarSiguiendoRecurrente: function() {
		$("#contenidoBodyDivSiguiendo").unbind('scroll');
		
		var formData = new FormData;
		formData.append("idLider",		parametroUrl('lider'));
		formData.append("idPagina",		parametroUrl('id'));
		formData.append("idPrimero",	ContenidoCOMP.novedades.ultimo);
		formData.append("idUltimo",		ContenidoCOMP.novedades.primero);
		
		m.request({
			method: "POST",
			url: urlServicios + "seguir/listarPublicacionesPagina",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			if(data.publicaciones != null && data.publicaciones.length > 0) {
				for (i = 0; i < data.publicaciones.length; i++) {
					var j = 0;
					while( j < ContenidoCOMP.novedades.lista.length && data.publicaciones[i].id < ContenidoCOMP.novedades.lista[j].id ){
						j++;
					}
					ContenidoCOMP.novedades.lista.splice(j, 0, data.publicaciones[i]);
					ContenidoCOMP.novedades.listaNovedad.splice(j, 0, ContenidoCOMP.actualizarSiguiendo(data.publicaciones[i]));
				}
				ContenidoCOMP.novedades.primero = ContenidoCOMP.novedades.lista[0].id;
				ContenidoCOMP.novedades.ultimo = ContenidoCOMP.novedades.lista[ContenidoCOMP.novedades.lista.length - 1].id;
				ContenidoCOMP.novedades.actualizado = 2;
			}
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
	},
	actualizarNovedadesRecurrente: function() {
		$("#contenidoBodyDivSiguiendo").unbind('scroll');
		
		var formData = new FormData;
		formData.append("fechaMaxima",	ContenidoCOMP.novedades.ultimo);
			
		m.request({
			method: "POST",
			url: urlServicios + "seguir/muroPublico",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			if(data != null && data.length > 0) {
				for (i = 0; i < data.length; i++) {
					ContenidoCOMP.novedades.lista.push(data[i]);
					ContenidoCOMP.novedades.listaNovedad.push(ContenidoCOMP.actualizarNovedad(data[i]));
				}
				ContenidoCOMP.novedades.ultimo = data[data.length - 1].fechaModificacion;
				ContenidoCOMP.novedades.actualizado = 2;
			}
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
	},
	actualizarChat: function(data) {
		if( PaginaCOMP.usuario.idUsuario == data.usuario ) {
			return m("div", {class: "card mensaje-ajeno"}, m("div", {class: "card-content contenido-mensaje"}, [
				m("div", {class: "right contenido-mensaje-avatar"}, m("img", {class: "circle avatar", src: PaginaCOMP.usuario.avatar} , "")),
				m("div", {class: "contenido-mensaje-texto contenido-mensaje-texto-left"}, "Tú: " + data.mensaje),
			]));
		} else {
			return m("div", {class: "card mensaje-propio"}, m("div", {class: "card-content contenido-mensaje"}, [
				m("div", {class: "left contenido-mensaje-avatar"}, m("img", {class: "circle avatar", src: ContenidoCOMP.mesajes.avatarOtro} , "")),
				m("div", {class: "contenido-mensaje-texto contenido-mensaje-texto-right"}, data.nombre + ": " + data.mensaje),
			]));
		}
	},
	actualizarChatRecurrente: function() {
		$("#contenidoBodyDivChat").unbind('scroll');
		var formData = new FormData;
		formData.append("idChat",	parametroUrl('id'));
		formData.append("idMensajeMinimo",	ContenidoCOMP.mesajes.ultimo);
		formData.append("idMensajeMaximo",	ContenidoCOMP.mesajes.primero);
		
		m.request({
			method: "POST",
			url: urlServicios + "mensaje/leerChat",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			if(data != null && data.length > 0) {
				for (i = 0; i < data.length; i++) {
					var j = 0;
					while( j < ContenidoCOMP.mesajes.lista.length && data[i].id > ContenidoCOMP.mesajes.lista[j].id ){
						j++;
					}
					ContenidoCOMP.mesajes.lista.splice(j, 0, data[i]);
					ContenidoCOMP.mesajes.listaMensajes.splice(j, 0, ContenidoCOMP.actualizarChat(data[i]));
				}
				ContenidoCOMP.mesajes.primero = ContenidoCOMP.mesajes.lista[ContenidoCOMP.mesajes.lista.length - 1].id;
				ContenidoCOMP.mesajes.ultimo = ContenidoCOMP.mesajes.lista[0].id;
				ContenidoCOMP.mesajes.actualizado = 2;
			}
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
	},
	actualizarChatRecurrenteNuevos: function() {
		var formData = new FormData;
		formData.append("idChat",	parametroUrl('id'));
		formData.append("idMensajeMaximo",	ContenidoCOMP.mesajes.primero);
		
		m.request({
			method: "POST",
			url: urlServicios + "mensaje/leerChatNuevos",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			if(data != null && data.length > 0) {
				for (i = 0; i < data.length; i++) {
					var j = 0;
					while( j < ContenidoCOMP.mesajes.lista.length && data[i].id > ContenidoCOMP.mesajes.lista[j].id ){
						j++;
					}
					ContenidoCOMP.mesajes.lista.splice(j, 0, data[i]);
					ContenidoCOMP.mesajes.listaMensajes.splice(j, 0, ContenidoCOMP.actualizarChat(data[i]));
				}
				ContenidoCOMP.mesajes.primero = ContenidoCOMP.mesajes.lista[ContenidoCOMP.mesajes.lista.length - 1].id;
				ContenidoCOMP.mesajes.ultimo = ContenidoCOMP.mesajes.lista[0].id;
			}
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
	},
	enviarMensajeAccion: function() {
		var texto = $("#contenidoBodyChatInputText").val().trim();
		
		if(texto.length > 0) {
			$("#contenidoBodyChatInputText").val("");
			
			if( parametroUrl('id') == null ) {
				var formData = new FormData;
				
				formData.append("idReceptor",	parametroUrl('lider'));
				formData.append("mensaje",		texto);
				
				m.request({
					method: "POST",
					url: urlServicios + "mensaje/escribir",
					body: formData,
					serialize: function(value) {return value}
				})
				.then(function(data) {
					
					var formData = new FormData;
					
					formData.append("lider",	parametroUrl('lider'));
					
					m.request({
						method: "POST",
						url: urlServicios + "mensaje/buscarChat",
						body: formData,
						serialize: function(value) {return value}
					})
					.then(function(data) {
						if( data != null && data.id != null ) {
							PaginaCOMP.cambiarUrl("/chat?id=" + data.id);
						}
					})
					.catch(function(data) {
						M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
					});
				})
				.catch(function(data) {
					M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
				});
			} else {
				var formData = new FormData;
				
				formData.append("idReceptor",	ContenidoCOMP.mesajes.otroId);
				formData.append("mensaje",		texto);
				
				m.request({
					method: "POST",
					url: urlServicios + "mensaje/escribir",
					body: formData,
					serialize: function(value) {return value}
				})
				.then(function(data) {
					ContenidoCOMP.actualizarChatRecurrenteNuevos();
				})
				.catch(function(data) {
					M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
				});
			}
		}
    },
	seguirAccion: function(idPagina, idLider) {
		$("a#lider-pagina-seguir").css("display", "none");
		var formData = new FormData;
		
		formData.append("idLider",	idLider);
		formData.append("idPagina",	idPagina);
		
		m.request({
			method: "POST",
			url: urlServicios + "seguir/seguirPagina",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			ContenidoCOMP.actualizarUrl();
			PaginaCOMP.actualizarSiguiendo();
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	dejarSeguirAccion: function(idPagina, idLider) {
		$("a#lider-pagina-dejarseguir").css("display", "none");
		var formData = new FormData;
		
		formData.append("idLider",	idLider);
		formData.append("idPagina",	idPagina);
		
		m.request({
			method: "POST",
			url: urlServicios + "seguir/dejarSeguirPagina",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			ContenidoCOMP.actualizarUrl();
			PaginaCOMP.actualizarSiguiendo();
		})
		.catch(function(data) {
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	meGustaAccion: function(idPublicacion, idPagina, idLider) {
		$("#bot_megusta_" + idPublicacion).css("display","none");
		$("#text_megusta_" + idPublicacion).css("display","none");

		var formData = new FormData;
		
		formData.append("idLider",	idLider);
		formData.append("idPagina",	idPagina);
		formData.append("idPublicacion",	idPublicacion);
		
		m.request({
			method: "POST",
			url: urlServicios + "seguir/likePublicacion",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			$("#bot_nomegusta_" + idPublicacion).css("display","");
			$("#text_nomegusta_" + idPublicacion).css("display","");
		})
		.catch(function(data) {
			$("#bot_megusta_" + idPublicacion).css("display","");
			$("#text_megusta_" + idPublicacion).css("display","");
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	nomeGustaAccion: function(idPublicacion, idPagina, idLider) {
		$("#bot_nomegusta_" + idPublicacion).css("display","none");
		$("#text_nomegusta_" + idPublicacion).css("display","none");

		var formData = new FormData;
		
		formData.append("idLider",	idLider);
		formData.append("idPagina",	idPagina);
		formData.append("idPublicacion",	idPublicacion);
		
		m.request({
			method: "POST",
			url: urlServicios + "seguir/dislikePublicacion",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			$("#bot_megusta_" + idPublicacion).css("display","");
			$("#text_megusta_" + idPublicacion).css("display","");
		})
		.catch(function(data) {
			$("#bot_nomegusta_" + idPublicacion).css("display","");
			$("#text_nomegusta_" + idPublicacion).css("display","");
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	paginaUsuarioAnterior: function() {
		var pagina = 1;
		
		try {
			pagina = parseInt($('#form_bus_usu_pag').val());
			pagina--;
		} catch (error) {}
		
		if( pagina < 1 ) {
			pagina = 1;
		}
		
		$('#form_bus_usu_pag').val(pagina);
    },
	paginaUsuarioSiguiente: function() {
		var pagina = 1;
		
		try {
			pagina = parseInt($('#form_bus_usu_pag').val());
			pagina++;
		} catch (error) {}
		
		if( pagina < 1 ) {
			pagina = 1;
		}
		
		$('#form_bus_usu_pag').val(pagina);
    },
	paginaUsuarioBuscar: function() {
		abrirModal("#modalCargando");
		ContenidoCOMP.listadoBusquedaUsuarios = [];
		
		var formData = new FormData;
		
		formData.append("pagina",				$('#form_bus_usu_pag').val());
		formData.append("nick",					$('#form_bus_usu_nick').val());
		formData.append("nombre",				$('#form_bus_usu_nom').val());
		formData.append("email",				$('#form_bus_usu_email').val());
		formData.append("tipoUsuario",			$('#form_bus_usu_tipo').val());
		formData.append("estaVerificado",		$('#form_bus_usu_verificado').val());
		formData.append("estaBloqueado",		$('#form_bus_usu_bloqueado').val());
		formData.append("fechaCreacionDesde",	prepararFecha($('#form_bus_usu_fec_des').val()));
		formData.append("fechaCreacionHasta",	prepararFecha($('#form_bus_usu_fec_has').val()));
		
		m.request({
			method: "POST",
			url: urlServicios + "administrador/listarUsuarios",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			cerrarModal("#modalCargando");
			abrirModal("#modalListaUsuarios");
			if( data != null ) {
				for (var i = 0; i < data.length; i++) {
					ContenidoCOMP.listadoBusquedaUsuarios.push(m("tr", [
							m("td", m("a", {href:"javascript:ContenidoCOMP.paginaUsuarioDetalle(" + data[i].id + ");"},data[i].nombre)),
							m("td", m("a", {href:"javascript:ContenidoCOMP.paginaUsuarioDetalle(" + data[i].id + ");"},data[i].email)),
							m("td", m("a", {href:"javascript:ContenidoCOMP.paginaUsuarioDetalle(" + data[i].id + ");"},data[i].nick)),
							m("td", m("a", {href:"javascript:ContenidoCOMP.paginaUsuarioDetalle(" + data[i].id + ");"},data[i].tipoUsuario == 1 ? "A" : data[i].tipoUsuario == 2 ? "L" : "D")),
					]));
				}
			} else {
				ContenidoCOMP.listadoBusquedaUsuarios.push(m("tr", {class: ""}, [
					m("td", {class: "center", colspan: "4"}, "No se encontraron usuarios."),
				]));
			}
			m.render(document.getElementById("modalListaUsuariosRender"), ContenidoCOMP.listadoBusquedaUsuarios);
		})
		.catch(function(data) {
			cerrarModal("#modalCargando");
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
    },
	paginaUsuarioDetalle: function(id) {
		cerrarModal("#modalListaUsuarios");
		abrirModal("#modalCargando");
		
		ContenidoCOMP.listadoBusquedaUsuariosPaginas = [];
		
		var formData = new FormData;
		
		formData.append("idUsuario", id);
		
		m.request({
			method: "POST",
			url: urlServicios + "administrador/getUsuario",
			body: formData,
			serialize: function(value) {return value}
		})
		.then(function(data) {
			cerrarModal("#modalCargando");
			abrirModal("#modalDetalleUsuario");
			ContenidoCOMP.usuarioBusqueda = data;
			if( data.paginas.length > 0 ) {
				ContenidoCOMP.listadoBusquedaUsuariosPaginas.push( m("h5", {class: "input-field center col s12"}, "Paginas"));
				for (var i = 0; i < data.paginas.length; i++) {
					ContenidoCOMP.listadoBusquedaUsuariosPaginas.push( m("div", {class: "input-field col s12"},[
						m("i", {class: "material-icons prefix"}, "web"),
						m("div", {class: "modal-configurar-label"}, "Nombre: " + data.paginas[i].nombre),
					]));
					ContenidoCOMP.listadoBusquedaUsuariosPaginas.push( m("div", {class: "input-field col s12 m6"},[
						m("i", {class: "material-icons prefix"}, "block"),
						m("div", {class: "modal-configurar-label"}, "Bloqueada: " + (data.paginas[i].bloqueada == 0 ? 'No' : 'Si')),
					]));
					ContenidoCOMP.listadoBusquedaUsuariosPaginas.push( m("div", {class: "input-field col s12 m6 botones-configurar"},[
								m("a", {class: "btn", href:"javascript:LateralCOMP.administrarCambiarBloqueadoPagina(" + data.id + "," + data.paginas[i].id + "," + data.paginas[i].bloqueada + ");"}, "Modificar"),    
					]));
					ContenidoCOMP.listadoBusquedaUsuariosPaginas.push( m("h5", {class: "input-field center col s12"}, "- - - - -"));
				}
			}
			m.render(document.getElementById("listadoBusquedaUsuariosPaginas"), ContenidoCOMP.listadoBusquedaUsuariosPaginas);
			console.log(data);
		})
		.catch(function(data) {
			cerrarModal("#modalCargando");
			abrirModal("#modalListaUsuarios");
			M.toast({html: data.response.errorMensaje, classes: 'rounded red'});
		});
		
    },
	paginaUsuarioLimpiar: function() {
		$('#form_bus_usu_pag').val("1");
		$('#form_bus_usu_nick').val("");
		$('#form_bus_usu_nom').val("");
		$('#form_bus_usu_email').val("");
		$('#form_bus_usu_tipo').find('option:eq("")').prop('selected', true);
		$('#form_bus_usu_verificado').find('option:eq("")').prop('selected', true);
		$('#form_bus_usu_bloqueado').find('option:eq("")').prop('selected', true);
		$('#form_bus_usu_fec_des').val("");
		$('#form_bus_usu_fec_has').val("");
		
		$('div#contenidoBodyDivAdmin select').formSelect();
    },
    oncreate: function(vnode) {
    },
    onupdate: function(vnode) {
		if( ContenidoCOMP.tipo == "CHA" ) {
			if( ContenidoCOMP.mesajes.actualizado == 1 ) {
				ContenidoCOMP.mesajes.actualizado = 0;
				document.getElementById("contenidoBodyDivChat").scrollTop = ContenidoCOMP.mesajes.posicion;
				ContenidoCOMP.mesajes.posicion = document.getElementById("contenidoBodyDivChat").clientHeight - document.getElementById("contenidoBodyDivChat").scrollHeight;
				
				$("#contenidoBodyDivChat").unbind('scroll');
				$("#contenidoBodyDivChat").scroll(function(){
					if( document.getElementById("contenidoBodyDivChat").scrollTop < document.getElementById("contenidoBodyDivChat").clientHeight - document.getElementById("contenidoBodyDivChat").scrollHeight + 1) {
						ContenidoCOMP.actualizarChatRecurrente();
					}
				});
			}
			if( ContenidoCOMP.mesajes.actualizado == 2 ) {
				ContenidoCOMP.mesajes.actualizado = 0;
				document.getElementById("contenidoBodyDivChat").scrollTop = ContenidoCOMP.mesajes.posicion;
				ContenidoCOMP.mesajes.posicion = document.getElementById("contenidoBodyDivChat").clientHeight - document.getElementById("contenidoBodyDivChat").scrollHeight;
				$("#contenidoBodyDivChat").scroll(function(){
					if( document.getElementById("contenidoBodyDivChat").scrollTop < document.getElementById("contenidoBodyDivChat").clientHeight - document.getElementById("contenidoBodyDivChat").scrollHeight + 1) {
						ContenidoCOMP.actualizarChatRecurrente();
					}
				});
			}
		}
		if( ContenidoCOMP.tipo == "SIG" ) {
			if( ContenidoCOMP.novedades.actualizado == 1 ) {
				if( window.location.hash.search("#/siguiendo") == 0 ) {
					ContenidoCOMP.novedades.actualizado = 0;
					document.getElementById("contenidoBodyDivSiguiendo").scrollTop = ContenidoCOMP.novedades.posicion;
					ContenidoCOMP.novedades.posicion = document.getElementById("contenidoBodyDivSiguiendo").clientHeight - document.getElementById("contenidoBodyDivSiguiendo").scrollHeight;
					
					$("#contenidoBodyDivSiguiendo").unbind('scroll');
					$("#contenidoBodyDivSiguiendo").scroll(function(){
						if( document.getElementById("contenidoBodyDivSiguiendo").scrollTop > document.getElementById("contenidoBodyDivSiguiendo").scrollHeight - document.getElementById("contenidoBodyDivSiguiendo").clientHeight - 1) {
							ContenidoCOMP.actualizarSiguiendoRecurrente();
						}
					});
					
					$('.slider').slider({
						height : 300
					});
				}
				if( window.location.hash.search("#/novedades") == 0 ) {
					ContenidoCOMP.novedades.actualizado = 0;
					document.getElementById("contenidoBodyDivSiguiendo").scrollTop = ContenidoCOMP.novedades.posicion;
					ContenidoCOMP.novedades.posicion = document.getElementById("contenidoBodyDivSiguiendo").clientHeight - document.getElementById("contenidoBodyDivSiguiendo").scrollHeight;
					
					$("#contenidoBodyDivSiguiendo").unbind('scroll');
					$("#contenidoBodyDivSiguiendo").scroll(function(){
						if( document.getElementById("contenidoBodyDivSiguiendo").scrollTop > document.getElementById("contenidoBodyDivSiguiendo").scrollHeight - document.getElementById("contenidoBodyDivSiguiendo").clientHeight - 1) {
							ContenidoCOMP.actualizarNovedadesRecurrente();
						}
					});
					
					$('.slider').slider({
						height : 300
					});
				}
			}
			if( ContenidoCOMP.novedades.actualizado == 2 ) {
				if( window.location.hash.search("#/siguiendo") == 0 ) {
					ContenidoCOMP.novedades.actualizado = 0;
					$("#contenidoBodyDivSiguiendo").scroll(function(){
						if( document.getElementById("contenidoBodyDivSiguiendo").scrollTop > document.getElementById("contenidoBodyDivSiguiendo").scrollHeight - document.getElementById("contenidoBodyDivSiguiendo").clientHeight - 1) {
							ContenidoCOMP.actualizarSiguiendoRecurrente();
						}
					});
					
					$('.slider').slider({
						height : 300
					});
				}
				
				if( window.location.hash.search("#/novedades") == 0 ) {
					ContenidoCOMP.novedades.actualizado = 0;
					$("#contenidoBodyDivSiguiendo").scroll(function(){
						if( document.getElementById("contenidoBodyDivSiguiendo").scrollTop > document.getElementById("contenidoBodyDivSiguiendo").scrollHeight - document.getElementById("contenidoBodyDivSiguiendo").clientHeight - 1) {
							ContenidoCOMP.actualizarNovedadesRecurrente();
						}
					});
					
					$('.slider').slider({
						height : 300
					});
				}
			}
		}
    },
    onbeforeupdate: function(newVnode, oldVnode) {
        return true;
    },
	/*
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
		return m("div",[
			m("div", {style: ContenidoCOMP.tipo == "CAR" ? "" : "display: none"}, [
				m("img", {class: "cargando", src: urlCargando} , ""),
			]),
			m("div", {style: ContenidoCOMP.tipo == "CHA" ? "" : "display: none"}, [
				m("div#contenidoBodyDivChat", {class: "contenidoBody"}, [
					m("div#contenidoBodyMensajes", ContenidoCOMP.mesajes.listaMensajes),
					m("div#contenidoBodyChat", [
						m("div#contenidoBodyChatInput", [
							m("input#contenidoBodyChatInputText", {class: "validate requerido", type:"text"}, ""),
							m("a", {class: "btn-floating waves-effect waves-light btn black", href: "javascript:ContenidoCOMP.enviarMensajeAccion();"},[
								m("i", {class: "large material-icons"}, "arrow_forward"),
							]),
						])
					])
				]),
			]),
			m("div", {style: ContenidoCOMP.tipo == "SIG" ? "" : "display: none"}, [
				m("div#contenidoBodyDivSiguiendo", {class: "contenidoBody"}, [
					ContenidoCOMP.novedades.usuarioNovedad,
					ContenidoCOMP.novedades.listaNovedad,
				]),
			]),
			m("div", {style: ContenidoCOMP.tipo == "LID" ? "" : "display: none"}, [
				m("div#contenidoBodyDivLider", {class: "contenidoBody"}, [
					m("div", {class: "card publicacion-card"}, [
						m("div", {class: "card-image"}, [
							m("img", {src: ContenidoCOMP.lider.avatar}, ""),
							m("div", {class: "lider-avatar-abajo"}, [
								m("span", {class: "card-title"}, ContenidoCOMP.lider.nombre + " (" + ContenidoCOMP.lider.nick + ")"),
								m("a", {class: "lider-avatar-abajo-escribir waves-effect waves-teal btn-flat grey lighten-4 right", style: ContenidoCOMP.lider.enviarMensaje == 0 ? "display: none" : "", href: "javascript:PaginaCOMP.cambiarUrl('/chat?lider=" + parametroUrl('id') + "')"},[
									m("i", {class: "material-icons large left"}, "chat"),"Escribir"
								]),
							]),
						]),
						m("div", {class: "card-content"}, [
							m("div", {style: ContenidoCOMP.lider.propio == 0 ? "" : "display: none"}, ContenidoCOMP.lider.biografia),
							m("div#contenidoBodyDivBiografia", {style: ContenidoCOMP.lider.propio == 1 ? "" : "display: none"}, [
								m("div", {class: "col s12"},[
									m("div", {class: "row"},[
										m("div", {class: "input-field col s12 m12"},[
											m("i", {class: "material-icons prefix active"}, "chrome_reader_mode"),
											m("input", {id: "form_mod_bio", type:"text", class:"validate requerido valid"}, ""),
											m("label", {for: "form_mod_bio", id: "form_mod_bio_lab"}, "Biografía *"),
										]),
										m("div", {class: "input-field col s12 m12 botones-configurar"},[
											m("a", {class: "btn", href:"javascript:LateralCOMP.ejecutarModificarBiografia();"}, "Guardar"),
										]),
									]),
								]),
							]),
						]),
					]),
					m("div", ContenidoCOMP.lider.paginas),
				]),
			]),
			m("div", {style: ContenidoCOMP.tipo == "BUS" ? "" : "display: none"}, [
				m("div#contenidoBodyDivBusqueda", {class: "contenidoBody"}, [
					ContenidoCOMP.busqueda.pagina
				]),
			]),
			m("div", {style: ContenidoCOMP.tipo == "CHT" ? "" : "display: none"}, [
				m("div#contenidoBodyDivChatTodos", {class: "contenidoBody"}, [
					m("div", ContenidoCOMP.chatTodos),
				]),
			]),
			m("div", {style: ContenidoCOMP.tipo == "MOD" ? "" : "display: none"}, [
				m("div#contenidoBodyDivModificar", {class: "contenidoBody"}, [
					m("div", {class: "card publicacion-card"}, [
						m("h4", [
							"Modificar publicacion",
						]),
						m("div", {class: "col s12"},[
							m("div", {class: "row"},[
								m("div", {class: "input-field col s12 m12"},[
									m("i", {class: "material-icons prefix active"}, "chrome_reader_mode"),
									m("input", {id: "form_mod_pub_tex", type:"text", class:"validate requerido valid"}, ""),
									m("label", {for: "form_mod_pub_tex", id: "form_mod_pub_tex_lab"}, "Publicacion *"),
								]),
								m("div", {class: "input-field col s12 m12"},[
									ContenidoCOMP.modificarImagenes,
									m("a", {class: "btn agregar-imagen-miniatura left", href:"javascript:LateralCOMP.ejecutarModificarPublicacionAgregarImagen();"}, "Agregar Imagen"),
									m("input[type=file]", {id: "modificar-imagen-miniatura", style: "display: none", onchange: LateralCOMP.abrirModalModificarPublicacionChangeFile, accept:".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*"}, ""),
								]),
								m("div", {class: "input-field col s12 m12 botones-configurar"},[
									m("a", {class: "btn", href:"javascript:LateralCOMP.ejecutarBorrarPublicacion();"}, "Borrar"),
									m("a", {class: "btn", href:"javascript:LateralCOMP.ejecutarModificarPublicacion();"}, "Guardar"),
									m("a", {class: "btn", href:"javascript:PaginaCOMP.cambiarUrl('/siguiendo?id=" + parametroUrl('pagina') + "&lider=" + PaginaCOMP.usuario.idUsuario + "');"}, "Volver"),
								]),
							]),
						]),
					]),
				]),
			]),
			m("div", {style: ContenidoCOMP.tipo == "PAG" ? "" : "display: none"}, [
				m("div#contenidoBodyDivPagina", {class: "contenidoBody"}, [
					m("div", {class: "card publicacion-card"}, [
						m("h4", [
							"Modificar página",
						]),
						m("div", {class: "col s12"},[
							m("div", {class: "row"},[
								m("div", {class: "input-field col s12 m12"},[
									m("i", {class: "material-icons prefix active"}, "web"),
									m("input", {id: "form_mod_pag_nom", type:"text", class:"validate requerido valid"}, ""),
									m("label", {for: "form_mod_pag_nom", id: "form_mod_pag_nom_lab"}, "Nombre *"),
								]),
								m("div", {class: "input-field col s12 m12"},[
									m("i", {class: "material-icons prefix active"}, "chrome_reader_mode"),
									m("input", {id: "form_mod_pag_des", type:"text", class:"validate requerido valid"}, ""),
									m("label", {for: "form_mod_pag_des", id: "form_mod_pag_des_lab"}, "Descripcion *"),
								]),
								m("div", {class: "input-field col s12 m12 botones-configurar"},[
									m("a", {class: "btn", href:"javascript:LateralCOMP.ejecutarBorrarPagina();"}, "Borrar"),
									m("a", {class: "btn", href:"javascript:LateralCOMP.ejecutarModificarPagina();"}, "Guardar"),
									m("a", {class: "btn", href:"javascript:PaginaCOMP.cambiarUrl('/siguiendo?id=" + parametroUrl('id') + "&lider=" + PaginaCOMP.usuario.idUsuario + "');"}, "Volver"),
								]),
							]),
						]),
					]),
				]),
			]),
			m("div", {style: ContenidoCOMP.tipo == "ADM" ? "" : "display: none"}, [
				m("div#contenidoBodyDivAdmin", {class: "contenidoBody"}, [
					m("div", {class: "card publicacion-card contenedor-admin"}, [
						m("h4", [
							"Panel de administrador",
						]),
						m("div", {class: "col s12"},[
							m("div", {class: "row"},[
								m("div", {class: "input-field col s6 m4"},[
									m("a", {class: "btn", href:"javascript:LateralCOMP.abrirModificarTyc();"}, [
										m("i", {class: "material-icons left"}, "assignment"),
										m("span", "Términos y condiciones"),
									]),
								]),
							]),
						]),
					]),
					m("div", {class: "card publicacion-card contenedor-admin"}, [
						m("h4", [
							"Buscador de usuarios",
						]),
						m("div", {class: "col s12"},[
							m("div", {class: "row"},[
								m("div", {class: "input-field col s12 m6"},[
									m("i", {class: "material-icons prefix"}, "account_box"),
									m("input", {id: "form_bus_usu_nick", type:"text"}, ""),
									m("label", {for: "form_bus_usu_nick"}, "Nick"),
								]),
								m("div", {class: "input-field col s12 m6"},[
									m("i", {class: "material-icons prefix"}, "assignment"),
									m("input", {id: "form_bus_usu_nom", type:"text"}, ""),
									m("label", {for: "form_bus_usu_nom"}, "Nombre"),
								]),
								m("div", {class: "input-field col s12 m6"},[
									m("i", {class: "material-icons prefix"}, "mail_outline"),
									m("input", {id: "form_bus_usu_email", type:"text"}, ""),
									m("label", {for: "form_bus_usu_email"}, "Email"),
								]),
								m("div", {class: "input-field col s12 m6"},[
									m("i", {class: "material-icons prefix"}, "accessibility"),
									m("select", {id: "form_bus_usu_tipo"}, [
										m("option", {value: ""}, ""),
										m("option", {value: "2"}, "Lider"),
										m("option", {value: "3"}, "Deboto"),
									]),
									m("label", "Tipo usuario"),
								]),
								m("div", {class: "input-field col s12 m6"},[
									m("i", {class: "material-icons prefix"}, "check"),
									m("select", {id: "form_bus_usu_verificado"}, [
										m("option", {value: ""}, ""),
										m("option", {value: "0"}, "No"),
										m("option", {value: "1"}, "Si"),
									]),
									m("label", "¿Esta verificado?"),
								]),
								m("div", {class: "input-field col s12 m6"},[
									m("i", {class: "material-icons prefix"}, "block"),
									m("select", {id: "form_bus_usu_bloqueado"}, [
										m("option", {value: ""}, ""),
										m("option", {value: "0"}, "No"),
										m("option", {value: "1"}, "Si"),
									]),
									m("label", "¿Esta bloqueado?"),
								]),
								m("div", {class: "input-field col s12 m6"},[
									m("i", {class: "material-icons prefix"}, "date_range"),
									m("input", {id: "form_bus_usu_fec_des", type:"text", class: "datepicker"}, ""),
									m("label", {for: "form_bus_usu_fec_des"}, "Fecha de creacion desde"),
								]),
								m("div", {class: "input-field col s12 m6"},[
									m("i", {class: "material-icons prefix"}, "date_range"),
									m("input", {id: "form_bus_usu_fec_has", type:"text", class: "datepicker"}, ""),
									m("label", {for: "form_bus_usu_fec_has"}, "Fecha de creacion hasta"),
								]),
								m("div", {class: "input-field col s12 m12"},[
									m("i", {class: "material-icons prefix"}, "mail_outline"),
									m("input", {id: "form_bus_usu_pag", type:"text"}, ""),
									m("label", {id: "form_bus_usu_pag_lab", for: "form_bus_usu_pag"}, "Pagina *"),
								]),
								m("div", {class: "input-field col s12 m12 botones-paginado"},[
									m("a", {class: "btn", href:"javascript:ContenidoCOMP.paginaUsuarioAnterior();"}, "Anterior"),
									m("a", {class: "btn", href:"javascript:ContenidoCOMP.paginaUsuarioBuscar();"}, "Buscar"),
									m("a", {class: "btn", href:"javascript:ContenidoCOMP.paginaUsuarioLimpiar();"}, "Limpiar"),
									m("a", {class: "btn", href:"javascript:ContenidoCOMP.paginaUsuarioSiguiente();"}, "Siguiente"),
								]),
							]),
						]),
					]),
				]),
			]),
			
		]);
    }
}