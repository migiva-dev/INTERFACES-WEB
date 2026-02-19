<?php

// CREAR UN ARRAY CON NUMEROS
$numeros = array(1,3,5,7,9);

// MOSTRAR ARRAY 

echo "Array original: <br>";
var_dump($numeros);
echo "<br>";

// CAMBIAR EL SEGUNDO NUMERO (POSICION 1)
$numeros[1] = 17;

//MOSTRAR ARRAY MODIFICADO
echo "Array modificado: <br>";
var_dump($numeros);
echo "<br>";

// ELIMINAR EL ULTIMO ELEMENTO DEL ARRAY
$ultimo = array_pop($numeros);
echo "Numero eliminado: $ultimo <br>";
echo "Array despues de eliminar el ultimo elemento ($ultimo): <br>";
var_dump($numeros);
echo "<br>";

?>