<?php

if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

function simplificar($str){
	$str = strtolower($str);
	
	$search  = array('á','à','ä','é','è','ë','ì','í','ï','ò','ó','ö','ù','ú','ü','ý','ÿ','ñ','v','k','s','z','x','n',' ','h');
	$replace = array('a','a','a','e','e','e','i','i','i','o','o','o','u','u','u','y','y','n','b','c','c','c','c','m','' ,'' );
	return str_replace($search, $replace, $str);
}

function caracteresInvalidos($str){
	$invalidos  = array('\\','/','!','&','|','"','\'','<','>');
	
	foreach ($invalidos as &$valor) {
		if( str_contains($str, $valor) ) {
			return true;
		}
	}
	
	return false;
}

function emailInvalido($str)
{
	$matches = null;
	return !(1 === preg_match('/^[A-z0-9\\._-]+@[A-z0-9][A-z0-9-]*(\\.[A-z0-9_-]+)*\\.([A-z]{2,6})$/', $str, $matches));
}

function lenghtInvalido($str, $valor)
{
	if( strlen($str) < $valor ) {
		return true;
	}
	return false;
}

function fechaInvalida($str)
{
	$arr = explode('-', $str);
	if( count($arr) != 3 || strlen($arr[0]) != 4 || strlen($arr[1]) != 2 || strlen($arr[2]) != 2 ) {
		return true;
	}
	if(!checkdate(intval($arr[1], 10), intval($arr[2], 10), intval($arr[0], 10))) {
		return true;
	}
	return false;
}

function encriptadoMd5Azar($str)
{
	$fecha = new DateTime();
	return md5(md5(md5( $str . $fecha->getTimestamp() . $str . rand(10000000, 99999999))) . $str);
}

function encriptadoMd5($str)
{
	return md5(md5(md5( $str . $str )) . $str);
}

?>
