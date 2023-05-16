var CabeceraCOMP = {
    oninit: function(vnode) {
    },
    oncreate: function(vnode) {
        document.getElementById("cabecer-buscar-input").addEventListener("keypress", CabeceraCOMP.handleEnterBusquedaEvento);
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
	handleEnterBusquedaEvento: function(event) {
		if (event.code === 'Enter' || event.code === 'NumpadEnter') {
			CabeceraCOMP.buscar();
		}
	},
	buscar: function() {
		valor = $("input#cabecer-buscar-input").val();
		if(valor.trim().length > 0) {
			$("input#cabecer-buscar-input").val("");
			PaginaCOMP.cambiarUrl('/busqueda?valor="' + valor + '"');
		}
    },
    view: function(vnode) {
		return m("nav", {class: "grey lighten-2"},
			m("div", {class: "nav-wrapper"},[
				m("img", {class: "brand-logo right logo", src: "src/images/logo.png"} , ""),
				
				m("div", {class: "menu_navegar"}, [
					m("a", {class: "waves-effect waves-light btn-large grey darken-1", href: "javascript:PaginaCOMP.cambiarUrl('/novedades');"},[
						m("i", {class: "material-icons large left"}, "art_track"),"Novedades"
					]),
					m("a", {class: "waves-effect waves-light btn-large grey darken-1", href: "javascript:PaginaCOMP.cambiarUrl('/lider?id=" + PaginaCOMP.usuario.idUsuario + "');", style: PaginaCOMP.usuario.logueado == 1 && PaginaCOMP.usuario.puedePublicar == 1 ? "" : "display: none"},[
						m("i", {class: "material-icons large left"}, "assignment"),"Páginas"
					]),
					m("a", {class: "waves-effect waves-light btn-large grey darken-1", href: "javascript:PaginaCOMP.cambiarUrl('/admin');", style: PaginaCOMP.usuario.logueado == 1 && PaginaCOMP.usuario.puedeAdministrar == 1 ? "" : "display: none"},[
						m("i", {class: "material-icons large left"}, "assignment"),"Administrar"
					]),
				]),
				
				/*
				m("a", {class: "boto_flat waves-effect waves-teal btn-flat left"},[
					m("i", {class: "material-icons large left"}, "search"),""
				]),
				*/
				
				m("a#cabecer-buscar-boton", {class: "boto_flat_circle btn-floating waves-effect waves-light btn grey darken-1 left", href: "javascript:CabeceraCOMP.buscar();"},[
					m("i", {class: "large material-icons"}, "search"),""
				]),
				
				m("input#cabecer-buscar-input", {class: "menu_buscar left", type: "text"}, ""),
			])
		);
    }
	
}