<?php

class Model{

    private $response;

    function __construct(){
        $this->db = new Database();
        $this->session = new Session($this->db);
    }

    function query($query){
        return $this->db->connect()->query($query);
    }

    function procedure($prepare, $execute, $select){
        $conexion = $this->db->connect();
        $prepare = $conexion->prepare($prepare);
        $prepare->execute($execute);
        if ( $select == NULL ) {
            return;
        }
        $query = $conexion->query($select);
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    function prepare($query){
        return $this->db->connect()->prepare($query);
    }

    function returnError($id, $datos = NULL){
        return array('error' => true, 'errorId' => $id, 'errorDatos' => $datos);
    }

}

?>