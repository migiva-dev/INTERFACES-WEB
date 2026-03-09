<?php

/*
=====================================================================
PASOS PARA USAR MONGODB CON PHP (RESUMEN COMPLETO)
=====================================================================

Instalar MongoDB Server
--------------------------------
Descargar desde:
https://www.mongodb.com/try/download/community

Instalar y arrancar el servidor.

Para arrancarlo manualmente desde terminal:

"& "C:\Program Files\MongoDB\Server\8.2\bin\mongod.exe" --dbpath C:\mongodb\data


Instalar extensión MongoDB en PHP
--------------------------------
Descargar la DLL correspondiente a tu versión de PHP desde: (ejecutar datos.php para comprobar version)
https://pecl.php.net/package/mongodb

Copiar el archivo:

php_mongodb.dll

a la carpeta:

C:\xampp\php\ext\


Activar extensión en php.ini
--------------------------------

Abrir:
C:\xampp\php\php.ini

Añadir la línea:

extension=mongodb

Reiniciar Apache desde XAMPP.

Instalar composer
--------------------------------

https://getcomposer.org/download/

Comprobar que la extensión funciona
--------------------------------

En terminal ejecutar:

php -m

Debe aparecer:

mongodb


Instalar Composer (gestor de dependencias)
--------------------------------

Ir a la carpeta del proyecto:

cd "C:\xampp\htdocs\daw2servidor\006-Utilización de técnicas de acceso a datos\005-Utilización de otros orígenes de datos\002-Ejercicios"

Ejecutar:

composer require mongodb/mongodb

Esto crea:

vendor/autoload.php


Usar el autoload de Composer
--------------------------------

Este archivo carga automáticamente todas las librerías instaladas.


Abrir el servidor
--------------------------------

Crear una carpeta donde se vaya a almacenar la base de datos. C:\mongodb\data

Ejecutar el siguiente comando para arrancar el servidor.

& "C:\Program Files\MongoDB\Server\8.2\bin\mongod.exe" --dbpath C:\mongodb\data
*/

require 'vendor/autoload.php';


use MongoDB\Client;


// Conectar con el servidor MongoDB
$client = new Client("mongodb://localhost:27017");


// Seleccionar base de datos
// Si no existe MongoDB la crea automáticamente
$db = $client->Videojuegos;


// Seleccionar colección
// En MongoDB una colección equivale a una tabla en SQL
$collection = $db->Juegos;


// Obtener todos los documentos de la colección
// Equivalente SQL:
// SELECT * FROM Juegos
$cursor = $collection->find();


// Recorrer los documentos obtenidos

$cursor = $collection->find();

foreach ($cursor as $juego) {

    if (isset($juego["_id"])) {
        echo "ID: " . $juego["_id"] . "<br>";
    }

    if (isset($juego["titulo"])) {
        echo "Título: " . $juego["titulo"] . "<br>";
    }

    if (isset($juego["fecha_lanzamiento"])) {
        echo "Fecha de lanzamiento: " . $juego["fecha_lanzamiento"] . "<br>";
    }

    if (isset($juego["pegi"])) {
        echo "PEGI: " . $juego["pegi"] . "<br>";
    }

    if (isset($juego["precio_base"])) {
        echo "Precio base: " . $juego["precio_base"] . "<br>";
    }

    if (isset($juego["motor"])) {
        echo "Motor: " . $juego["motor"] . "<br>";
    }

    if (isset($juego["es_multijugador"])) {
        echo "Es multijugador: " . ($juego["es_multijugador"] ? "Sí" : "No") . "<br>";
    }

    if (isset($juego["descripcion"])) {
        echo "Descripción: " . $juego["descripcion"] . "<br>";
    }

    if (isset($juego["juego_padre"])) {
        echo "Juego padre: " . $juego["juego_padre"] . "<br>";
    }

    if (isset($juego["estudio"])) {
        echo "<strong>Estudio:</strong><br>";

        if (isset($juego["estudio"]["nombre"])) {
            echo "Nombre: " . $juego["estudio"]["nombre"] . "<br>";
        }

        if (isset($juego["estudio"]["pais"])) {
            echo "País: " . $juego["estudio"]["pais"] . "<br>";
        }

        if (isset($juego["estudio"]["ciudad"])) {
            echo "Ciudad: " . $juego["estudio"]["ciudad"] . "<br>";
        }

        if (isset($juego["estudio"]["fundado_en"])) {
            echo "Fundado en: " . $juego["estudio"]["fundado_en"] . "<br>";
        }

        if (isset($juego["estudio"]["web"])) {
            echo "Web: " . $juego["estudio"]["web"] . "<br>";
        }

        if (isset($juego["estudio"]["telefono"])) {
            echo "Teléfono: " . $juego["estudio"]["telefono"] . "<br>";
        }

        if (isset($juego["estudio"]["correo"])) {
            echo "Correo: " . $juego["estudio"]["correo"] . "<br>";
        }
    }

    if (isset($juego["dlcs"]) && count($juego["dlcs"]) > 0) {
        echo "<strong>DLCs:</strong><br>";

        foreach ($juego["dlcs"] as $dlc) {
            if (isset($dlc["titulo"])) {
                echo "Título DLC: " . $dlc["titulo"] . "<br>";
            }

            if (isset($dlc["fecha_lanzamiento"])) {
                echo "Fecha DLC: " . $dlc["fecha_lanzamiento"] . "<br>";
            }

            if (isset($dlc["precio"])) {
                echo "Precio DLC: " . $dlc["precio"] . "<br>";
            }

            echo "<br>";
        }
    }

    echo "<hr>";
}
