<?php

// Constante llamada APP_NAME y le asignamos el valor "Gestor de Comandas PHP" y se muestre en pantalla

const APP_NAME = "Gestor de Comandas PHP";
echo "<h1>" . APP_NAME . "</h1>";

// Declaracion de variables de distintos tipos de datos

$producto = "Tortilla de trufa"; // String
$precio = 10.20; // Float
$cantidad = 1; // Integer
$disponible = true; // Boolean

// Mostrar en pantalla el tipo de dato de cada variable con var_dump()
echo "<br><br>";
echo "<br> Producto: " .var_dump($producto);
echo "<br> Precio: " . var_dump($precio);
echo "<br> Cantidad: " . var_dump($cantidad);
echo "<br> Disponible: " . var_dump($disponible);

//Mostar en pantalla el tipo de dato de cada variable con gettype()
echo "<br><br>";
echo "Tipo de dato de la variable producto: " . gettype($producto);
echo "<br> Tipo de dato de la variable precio: " . gettype($precio);
echo "<br> Tipo de dato de la variable cantidad: " . gettype($cantidad);
echo "<br> Tipo de dato de la variable disponible: " . gettype($disponible);

$comanda = "- Ensalada: 1, - Paella: 2, - Tarta de quesito: 2";

// Recoger variables por la URL con GET 
$usuario = $_GET['usuario'];
$mesa = $_GET['mesa']; 

// Mostrar en pantalla el usuario y la mesa que ha reservado y usamos los puntos para concatenar las variables.
echo "<br><br> El usuario " . $usuario . " ha reservado la mesa " . $mesa . ". Tome asiento<br>";
echo "<br>";
//Convertimos la variable $usuario a mayusculas.
$usuario = strtoupper($usuario) . "<br>";
echo "Usuario en mayusculas: " . $usuario;


//Calcular la longitud de la variable $usuario con strlen() y mostrarla en pantalla.
$longitud = strlen($usuario);
echo "La longitud del nombre de usuario es: " . $longitud . "<br>";
echo "<br>";


//Voy a descomponer la comanda en un array con explode() y mostrarlo en pantalla y que me quite los guiones.
$comanda_array = explode(", ", $comanda);
    $descomoponer = str_replace("- ", "", $comanda_array);
    echo "Comanda descompuesta en array: <br>";
    foreach ($descomoponer as $item) {
        echo $item . "<br>";
    }
echo "<br>";

//Buscar un tipo de plato especifico en la comanda con strpos() y mostrarlo en pantalla
$platoBuscado = "Paella";
$cantidad = 1;

foreach ($descomoponer as $item) {
    // Verifica si el plato buscado está dentro del elemento actual
    if (strpos($item, $platoBuscado) !== false) {
        echo "El plato " . $platoBuscado . " se encuentra en la comanda.<br>";
        echo "Cantidad: " . $cantidad . "<br>";
        $encontrado = true; // Marca que se encontró
        break; // Sale del bucle (ya lo encontró)
    }
}

// Si después del bucle no se encontró el plato
if (empty($encontrado)) {
    echo "El plato " . $platoBuscado . " no se encuentra en la comanda.<br>";
}

echo "<br>";

//Comparar el nombre de usuario introducido mediante la URL con su version en mayusculas.
//Uso Strcmp() para comparar las mayusculas y minusculas. 
$usuarioOriginal = $_GET['usuario'];
if (strcmp($usuarioOriginal, strtoupper($usuarioOriginal)) === 0) {
    echo "El nombre de usuario está en mayúsculas.<br>";
} else {
    echo "El nombre de usuario no está en mayúsculas.<br>";
}
echo "<br>";
// Uso strcasecmp() para comparar sin tener en cuenta mayusculas y minusculas.
if (strcasecmp($usuarioOriginal, strtoupper($usuarioOriginal)) === 0) {
    echo "El nombre de usuario es igual.<br>";
} else {
    echo "El nombre de usuario no es igual.<br>";
}
echo "<br>";

//Declaro una funcion, que acceda a la variable GLOBALS y muestre su valor actualizado dentro de la función.
function pedidosEnCola() {
    $GLOBALS['cantidad'] += 1;
    echo "Cantidad de platos actualizada dentro de la función: " . $GLOBALS['cantidad'] . "<br>";
}
pedidosEnCola();
echo "<br>";

//Funciones de verificación de tipos de datos con is_string() e is_numeric()
if (is_string($producto)) {
    echo "La variable producto es una cadena de texto.<br>";
} else {
    echo "La variable producto no es una cadena de texto.<br>";
}

if (is_numeric($precio)) {
    echo "La variable precio es un número.<br>";
} else {
    echo "La variable precio no es un número.<br>";
}
echo "<br>";


?>



