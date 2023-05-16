<?php

class View{

    function __construct(){
    }

    function render($data = NULL, $code = 200, $cleanHeaders = true){
        $this->arr = $data;
		$this->code = $code;
        $this->cleanHeaders = $cleanHeaders;

        require 'views/index.php';
    }

}

?>