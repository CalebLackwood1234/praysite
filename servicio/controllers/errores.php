<?php

class Errores extends Controller{

    function __construct($id, $datos = NULL){
        parent::__construct();
		
		$this->loadModel('errores');
		$this->model->get($id);

		if( $datos != NULL ) {
			$this->model->setDatos($datos);
		}

		error_log($this->model->getBack());
		
		$this->view->render($this->model->render(), $this->model->getCodigo(), $this->model->getCabecera());
    }

}

?>