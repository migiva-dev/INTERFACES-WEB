<?php
class EstudiantePuntuacion{
    public $Juan =95;
    public $Maria = 88;
    public $Pedro = 72;
    public $Pepe = 10;
}

$puntuacionesObjeto = new EstudiantePuntuacion();

//Iterar sobre las propiedades públicas del objeto usando foreach
foreach ($puntuacionesObjeto as $clave => $valor ) {
    echo $clave . ": " . $valor . "<br></br>";

}
?>