<?php

require_once 'controllers/errores.php';

class App{

    function __construct(){

        $url = isset($_GET['url']) ? $_GET['url']: null;
        $url = rtrim($url, '/');
        $url = explode('/', $url);

        // cuando se ingresa sin definir controlador
        if(empty($url[0])){
            $controller = new Errores(101);
            return false;
        }
		
        $archivoController = 'controllers/' . $url[0] . '.php';

        if(file_exists($archivoController)){
            require_once $archivoController;

            // inicializar controlador
            $controller = new $url[0];
            $controller->loadModel($url[0]);

            // si hay un método que se requiere cargar
            if(isset($url[1])){
                if(method_exists($controller, $url[1])){
                    if(isset($url[2])){
                        //el método tiene parámetros
                        //sacamos e # de parametros
                        $nparam = sizeof($url) - 2;
                        //crear un arreglo con los parametros
                        $params = [];
                        //iterar
                        for($i = 0; $i < $nparam; $i++){
                            array_push($params, $url[$i + 2]);
                        }
                        //pasarlos al metodo   
                        $controller->{$url[1]}($params);
                    }else{
                        $controller->{$url[1]}();    
                    }
                }else{
                    error_log($url[1]);
                    $controller = new Errores(103);
                }
            }else{
                $controller->render();
            }
        }else{
            error_log($archivoController);
            $controller = new Errores(102);
        }
		
    }
}

?>