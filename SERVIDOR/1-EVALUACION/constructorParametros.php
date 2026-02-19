<?php

class Entrada{
    private $titulo;

    function __construct($nuevotitulo, $nuevocontenido){
        $this->titulo = $nuevotitulo;
        $this->fecha = "";
        $this->autor = "";
        $this->contenido = $nuevocontenido;
        $this->imagen = "";

    }
    function setTitulo(){
        if(strien($nuevotitulo) > 10){
            $this->titulo = $nuevotitulo;
        
        }else{
            $this->titulo = "error";
        }

    }
    function getTitulo(){
        return $this->titulo;
    }

}

$entrada1= new Entrada("Titulo de la entrada","Contenido de la entrada");



echo $entrada1 ->getTitulo();
$entrada1->setTitulo(nuevoTitulo: "Titulo nuevo");
echo $entrada1-> getTitulo();


?>