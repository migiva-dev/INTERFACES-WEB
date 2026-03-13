<?php
header("Content-Type: application/json; charset=utf-8");

require 'vendor/autoload.php';

use MongoDB\Client;

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$precioMax = (float)($data["precio_max"] ?? 0);
$anioMin = (int)($data["anio_min"] ?? 0);
$fechaMin = $anioMin . "-12-31";

try {
    // TODO 1:
    // Crear la conexión con el servidor MongoDB local.
    $client = new Client("mongodb://localhost:27017");

    // TODO 2:
    // Seleccionar la base de datos llamada Videojuegos.
    $bd = $client->Videojuegos;

    // TODO 3:
    // Seleccionar la colección llamada JuegosBase.
    $coleccion = $VideoJuegos->JuegosBase;

    $filtro = [
        "precio_base" => ['$lte' => $precioMax],
        "fecha_lanzamiento" => ['$gt' => $fechaMin]
    ];

    // TODO 4:
    // Ejecutar la consulta sobre la colección usando el filtro anterior
    $resultado = $coleccion->find($filtro);

    $juegos = [];

    // TODO 5:
    // Recorrer los documentos devueltos y construir el array $juegos
    foreach ($resultado as $documento) {

        $juegos[] = [
            "titulo" => $documento["titulo"] ?? "",
            "fecha_lanzamiento" => $documento["fecha_lanzamiento"] ?? "",
            "pegi" => $documento["pegi"] ?? "",
            "precio_base" => $documento["precio_base"] ?? "",
            "motor" => $documento["motor"] ?? "",
            "genero" => $documento["genero"] ?? "",
            "descripcion" => $documento["descripcion"] ?? ""
        ];
    }

    echo json_encode([
        "ok" => true,
        "total" => count($juegos),
        "juegos" => $juegos
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "error" => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}