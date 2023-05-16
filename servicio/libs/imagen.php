<?php

include_once 'ImageResize/ImageResize.php';
include_once 'ImageResize/ImageResizeException.php';
use \Gumlet\ImageResize;
use \Gumlet\ImageResizeException;

function resizeImagenMax($pathImagen, $anchoMaximo, $altoMaximo)
{
	$resize = new ImageResize($pathImagen);
	
	$resize->resizeToBestFit($anchoMaximo, $altoMaximo);

	$resize->save($pathImagen, null, null, constant('PERMISOS_ARCHIVOS'));
}

function resizeToShortSide($pathImagen, $maximo)
{
	$resize = new ImageResize($pathImagen);
	
	$resize->resizeToShortSide($maximo);
	$resize->crop($maximo, $maximo);

	$resize->save($pathImagen, null, null, constant('PERMISOS_ARCHIVOS'));
}

function esFormatoValido($tipo)
{
	return strpos($tipo, "jpeg") || strpos($tipo, "jpg") || strpos($tipo, "bmp") || strpos($tipo, "gif") || strpos($tipo, "png");
}

function esSizeValido($tamano)
{
	return $tamano <= 10000000;
}

function crearCarpeta($path)
{
	if (!file_exists($path)) {
		mkdir($path, constant('PERMISOS_ARCHIVOS'), true);
	}
}

function rmDir_rf($carpeta)
{
	foreach(glob($carpeta . "/*") as $archivos_carpeta){             
		if (is_dir($archivos_carpeta)){
				rmDir_rf($archivos_carpeta);
			} else {
				unlink($archivos_carpeta);
			}
		}
	rmdir($carpeta);
}

function borrarCarpeta($path)
{
	if (file_exists($path)) {
		rmDir_rf($path);
	}
}

function borrarArchivo($path)
{
	if (file_exists($path)) {
		unlink($path);
	}
}

function copiarArchivo($pathOrigen, $pathDestino)
{
	copy($pathOrigen,$pathDestino);
	chmod($pathDestino, constant('PERMISOS_ARCHIVOS'));
}

function moverArchivo($pathOrigen, $pathDestino)
{
	if (move_uploaded_file($pathOrigen, $pathDestino)) {
		chmod($pathDestino, constant('PERMISOS_ARCHIVOS'));
		return true;
	} else {
		return false;
	}
}

?>