<?php

$local = true;

if( $local ) {
	define('SESSION_PATH', '/praysite/servicio/');
	define('SESSION_DOMAIN', 'localhost');

	define('HOST', 'localhost');
	define('DB', 'redsocial');
	define('USER', 'root');
	define('PASSWORD', '');
} else {
	define('SESSION_PATH', '/praysite/servicio/');
	define('SESSION_DOMAIN', 'localhost');

	define('HOST', 'localhost');
	define('DB', 'redsocial');
	define('USER', 'root');
	define('PASSWORD', '');
}

define('CHARSET', 'utf8mb4');
define('ELEMENTOS_POR_PAGINA', 10);
define('MENSAJES_POR_CHAT', 20);
define('MAXIMO_CARACTERES_TEXT', 65000);
define('MAXIMO_CARACTERES_NOMBRE_CORTO', 100);
define('MAXIMO_CARACTERES_DESCRIPCION_CORTA', 100);
define('MAXIMO_CARACTERES_PREVIEW_MENSAJE', 50);

define('MAXIMO_PERFIL_MINI',		50);
define('MAXIMO_PERFIL_MED',			150);
define('MAXIMO_ANCHO_PUBLICACION',	1280);
define('MAXIMO_ALTO_PUBLICACION',	1280);

define('PERMISOS_ARCHIVOS',			0700);

define('UBICACION_IMAGENES_PERFIL', 'resources/image/perfiles/');
define('UBICACION_IMAGENES_PUBLICACION', 'resources/image/publicacion/');

?>