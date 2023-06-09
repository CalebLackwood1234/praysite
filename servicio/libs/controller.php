<?php

class Controller{

    function __construct(){
        $this->view = new View();
    }

    function loadModel($model){
        $url = 'models/'.$model.'model.php';

        if(file_exists($url)){
            require_once $url;

            $modelName = $model.'Model';
            $this->model = new $modelName();
        }
    }

    function existPOST($params){
        foreach ($params as $param) {
            if(!isset($_POST[$param])){
                return false;
            }
        }
        return true;
    }

    function existGET($params){
        foreach ($params as $param) {
            if(!isset($_GET[$param])){
                return false;
            }
        }
        return true;
    }

    function getGet($name){
        return $_GET[$name];
    }

    function getPost($name){
        return $_POST[$name];
    }

    function checkError($valor){
        if ($valor != NULL && is_array($valor) && array_key_exists('error', $valor) && $valor['error'] == true) {
            new Errores($valor['errorId']);
            return true;
        } else {
            return false;
        }
    }

}

?>