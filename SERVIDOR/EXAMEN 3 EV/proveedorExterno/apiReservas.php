<?php
// =====================================================
// SERVICIO 3: API REST EXTERNA DE RESERVAS
// =====================================================
// Este fichero simula una API externa. El cliente HTML
// no debe llamarlo directamente. El controlador principal
// lo consume mediante cURL.

$ruta = obtenerRutaApi();
$metodo = $_SERVER["REQUEST_METHOD"];

if ($ruta !== "reservas") {
    responderJsonApi(
        ["error" => "Recurso no encontrado."],
        404
    );

    exit;
}

if ($metodo === "GET") {
    consultarReservas();

    exit;
}

if ($metodo === "POST") {
    crearReserva();

    exit;
}

responderJsonApi(
    ["error" => "Método no permitido."],
    405
);

// =====================================================
// FUNCIONES DE LA API REST
// =====================================================

function consultarReservas()
{
    $reservas = leerReservas();

    if ($reservas === null) {
        responderJsonApi(
            ["error" => "No se pudo leer el fichero de reservas."],
            500
        );

        return;
    }

    responderJsonApi($reservas, 200);
}

function crearReserva()
{
    $textoRecibido = file_get_contents("php://input");

    $datos = json_decode($textoRecibido, true);

    if (!is_array($datos)) {
        responderJsonApi(
            ["error" => "El cuerpo de la petición debe ser JSON válido."],
            400
        );

        return;
    }

    $cliente = trim($datos["cliente"] ?? "");
    $pelicula = trim($datos["pelicula"] ?? "");
    $entradas = (int) ($datos["entradas"] ?? 0);

    if ($cliente === "" || $pelicula === "" || $entradas <= 0) {
        responderJsonApi(
            [
                "error" =>
                    "Debes indicar cliente, película y una cantidad de entradas válida."
            ],
            400
        );

        return;
    }

    $reservas = leerReservas();

    if ($reservas === null) {
        responderJsonApi(
            ["error" => "No se pudo leer el fichero de reservas."],
            500
        );

        return;
    }

    $nuevoId = obtenerSiguienteId($reservas);

    $nuevaReserva = [
        "id" => $nuevoId,
        "cliente" => $cliente,
        "pelicula" => $pelicula,
        "entradas" => $entradas
    ];

    $reservas[] = $nuevaReserva;

    if (!guardarReservas($reservas)) {
        responderJsonApi(
            ["error" => "No se pudo guardar la nueva reserva."],
            500
        );

        return;
    }

    // TODO 15:
    // Devolver la respuesta correspondiente a la creación correcta
    // de una reserva llamando a la función responderJsonApi().
    // Consultar la especificación OpenAPI para identificar el código HTTP
    // que debe enviarse y la estructura JSON de la respuesta.
    
}

// =====================================================
// FUNCIONES AUXILIARES DE LA API REST
// =====================================================

function obtenerRutaApi()
{
    $pathInfo = $_SERVER["PATH_INFO"] ?? "";

    if ($pathInfo !== "") {
        return trim($pathInfo, "/");
    }

    $scriptName = $_SERVER["SCRIPT_NAME"] ?? "";

    $requestUri = parse_url(
        $_SERVER["REQUEST_URI"] ?? "",
        PHP_URL_PATH
    );

    if (
        $scriptName !== "" &&
        strpos($requestUri, $scriptName) === 0
    ) {
        return trim(
            substr($requestUri, strlen($scriptName)),
            "/"
        );
    }

    return "";
}

function leerReservas()
{
    $rutaDatos = __DIR__ . "/../datos/reservas.json";

    if (!file_exists($rutaDatos)) {
        return null;
    }

    $texto = file_get_contents($rutaDatos);

    $reservas = json_decode($texto, true);

    return is_array($reservas) ? $reservas : null;
}

function guardarReservas($reservas)
{
    $rutaDatos = __DIR__ . "/../datos/reservas.json";

    $texto = json_encode(
        $reservas,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );

    return file_put_contents(
        $rutaDatos,
        $texto,
        LOCK_EX
    ) !== false;
}

function obtenerSiguienteId($reservas)
{
    $idMaximo = 0;

    foreach ($reservas as $reserva) {
        if (
            isset($reserva["id"]) &&
            $reserva["id"] > $idMaximo
        ) {
            $idMaximo = $reserva["id"];
        }
    }

    return $idMaximo + 1;
}

function responderJsonApi($datos, $codigoHttp)
{
    http_response_code($codigoHttp);

    header("Content-Type: application/json; charset=utf-8");

    echo json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
}