<?php
// =====================================================
// CONTROLADOR CENTRAL DEL PORTAL DE CINE
// =====================================================
// Recibe todas las peticiones del cliente y decide cómo
// comunicarse con cada uno de los tres servicios.

$ruta = obtenerRuta();
$metodo = $_SERVER["REQUEST_METHOD"];

// =====================================================
// SERVICIO 1: CARTELERA - PHP DEVUELVE HTML
// =====================================================

// TODO 1:
// Gestionar la petición del recurso cartelera.
// Si la ruta recibida es "cartelera", cargar el servicio correspondiente
// mediante require_once, ejecutar la función que devuelve la cartelera
// en formato HTML y finalizar la ejecución del controlador.
// La cartelera solamente debe aceptar peticiones GET.

if ($ruta === "cartelera") {
    if ($metodo !== "GET") {
        http_response_code(405);
        header("Content-Type: text/html; charset=utf-8");

        echo '<p class="error">La cartelera solamente permite peticiones GET.</p>';

        exit;
    }

    require_once __DIR__ . "/servicios/servicioCartelera.php";

    mostrarCartelera();

    exit;
}

// =====================================================
// SERVICIO 2: PRECIO DE ENTRADAS - SOAP
// =====================================================
if ($ruta === "entradas") {
    require_once __DIR__ . "/servicios/servicioEntradasSoap.php";

    if ($metodo !== "POST") {
        enviarFaultSoap(
            "El servicio SOAP solamente permite peticiones POST."
        );

        exit;
    }

    procesarPeticionEntradasSoap();

    exit;
}

// =====================================================
// SERVICIO 3: RESERVAS - API REST EXTERNA MEDIANTE CURL
// =====================================================
if ($ruta === "reservas") {
    if ($metodo !== "GET" && $metodo !== "POST") {
        responderJson(
            ["error" => "Método no permitido para reservas."],
            405
        );

        exit;
    }

    consumirApiReservasConCurl($metodo);

    exit;
}

// =====================================================
// RUTA NO ENCONTRADA
// =====================================================
responderJson(
    ["error" => "Ruta no encontrada."],
    404
);

// =====================================================
// FUNCIONES GENERALES DEL CONTROLADOR
// =====================================================

function obtenerRuta()
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

// =====================================================
// CONSUMO DE LA API EXTERNA MEDIANTE CURL
// =====================================================

function consumirApiReservasConCurl($metodo)
{
    if (!function_exists("curl_init")) {
        responderJson(
            [
                "error" =>
                    "La extensión cURL no está habilitada en PHP."
            ],
            500
        );

        return;
    }

    $urlProveedor = construirUrlProveedor(
        "proveedorExterno/apiReservas.php/reservas"
    );

    // TODO 16:
    // Inicializar una petición cURL para comunicarse con la API externa
    // de reservas utilizando la URL almacenada en $urlProveedor.
    
    //$curl
    $curl = curl_init($urlProveedor);

    if ($curl === false) {
        responderJson(
            [
                "error" =>
                    "No se pudo inicializar la conexión con la API externa."
            ],
            500
        );

        return;
    }

    // TODO 17:
    // Configurar las opciones comunes de la petición cURL.
    // La respuesta de la API externa debe guardarse en una variable
    // y la petición debe indicar que espera recibir JSON.
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        "Accept: application/json"
    ]);

    // TODO 18:
    // Si el método recibido por el controlador es GET,
    // configurar cURL para realizar una petición GET al proveedor externo.
    if ($metodo === "GET") {
        curl_setopt($curl, CURLOPT_HTTPGET, true);
    }

    if ($metodo === "POST") {
        // TODO 19:
        // Leer el cuerpo JSON que el cliente ha enviado al controlador.
        // Este mismo cuerpo deberá reenviarse después a la API externa.
        $cuerpoJson = file_get_contents("php://input");

        // TODO 20:
        // Configurar cURL para realizar una petición POST a la API externa.
        // Reenviar el cuerpo JSON recibido anteriormente e indicar mediante
        // cabeceras que se envía y se espera recibir información JSON.
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $cuerpoJson);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json; charset=utf-8",
            "Accept: application/json"
        ]);
    }

    // TODO 21:
    // Ejecutar la petición cURL.
    // Si la petición es correcta, obtener el código HTTP devuelto por la API
    // externa y reenviar al cliente dicho código junto con el cuerpo JSON recibido (ABAJO).
    
    // $respuestaProveedor
    $respuestaProveedor = curl_exec($curl);

    if ($respuestaProveedor === false) {
        $errorCurl = curl_error($curl);

        curl_close($curl);

        responderJson(
            [
                "error" =>
                    "No se pudo contactar con la API externa de reservas.",
                "detalle" => $errorCurl
            ],
            502
        );

        return;
    }

    // $codigoHttp
    $codigoHttp = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    curl_close($curl);

    if ($codigoHttp === 0) {
        responderJson(
            [
                "error" =>
                    "La API externa no ha devuelto un código HTTP válido."
            ],
            502
        );

        return;
    }

    // RESPUESTA
    http_response_code($codigoHttp);
    header("Content-Type: application/json; charset=utf-8");

    echo $respuestaProveedor;
}

// =====================================================
// CONSTRUCCIÓN DE LA URL DEL PROVEEDOR EXTERNO
// =====================================================

function construirUrlProveedor($rutaRelativa)
{
    $usaHttps =
        isset($_SERVER["HTTPS"]) &&
        $_SERVER["HTTPS"] !== "" &&
        $_SERVER["HTTPS"] !== "off";

    $protocolo = $usaHttps ? "https" : "http";

    $host = $_SERVER["HTTP_HOST"] ?? "localhost";

    $scriptName = str_replace(
        "\\",
        "/",
        $_SERVER["SCRIPT_NAME"] ?? "/controlador.php"
    );

    $carpetaProyecto = dirname($scriptName);

    $rutaCompleta = trim(
        $carpetaProyecto . "/" . ltrim($rutaRelativa, "/"),
        "/"
    );

    $partesRuta = explode("/", $rutaCompleta);

    $partesCodificadas = [];

    foreach ($partesRuta as $parte) {
        $partesCodificadas[] = rawurlencode(
            rawurldecode($parte)
        );
    }

    $rutaCodificada = implode("/", $partesCodificadas);

    return $protocolo . "://" .
        $host .
        "/" .
        $rutaCodificada;
}

// =====================================================
// RESPUESTAS JSON DEL CONTROLADOR
// =====================================================

function responderJson($datos, $codigoHttp)
{
    http_response_code($codigoHttp);

    header("Content-Type: application/json; charset=utf-8");

    echo json_encode(
        $datos,
        JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
    );
}