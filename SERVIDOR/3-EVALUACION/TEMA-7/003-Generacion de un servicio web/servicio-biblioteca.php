<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$wsdl = "http://localhost/biblioteca/servicio-biblioteca.wsdl";

/*
|--------------------------------------------------------------------------
| FUNCION: Leer cabecera SOAP de sesión
|--------------------------------------------------------------------------
*/
function obtenerCabeceraSesionDesdeXML()
{
    $xml = file_get_contents("php://input");

    if (!$xml) {
        return null;
    }

    $doc = new DOMDocument();
    $doc->loadXML($xml);

    $xpath = new DOMXPath($doc);
    $xpath->registerNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
    $xpath->registerNamespace('ses', 'urn:SesionBiblioteca');

    $idSesionNode = $xpath->query('//soap:Header/ses:SessionHeader/ses:idSesion')->item(0);
    $usuarioNode  = $xpath->query('//soap:Header/ses:SessionHeader/ses:usuario')->item(0);

    if (!$idSesionNode || !$usuarioNode) {
        return null;
    }

    return [
        'idSesion' => $idSesionNode->nodeValue,
        'usuario'  => $usuarioNode->nodeValue
    ];
}

/*
|--------------------------------------------------------------------------
| CLASE DEL SERVICIO SOAP
|--------------------------------------------------------------------------
*/
class ServicioBiblioteca
{
    private $sesion;

    public function __construct($sesion)
    {
        $this->sesion = $sesion;
    }

    private function validarSesion()
    {
        if (!$this->sesion) {
            throw new SoapFault("Client.Auth", "No se recibió cabecera SOAP de sesión.");
        }

        // Valores de ejemplo
        if (
            $this->sesion['usuario'] !== 'admin' ||
            $this->sesion['idSesion'] !== '12345'
        ) {
            throw new SoapFault("Client.Auth", "Sesión inválida.");
        }
    }

    public function consultarPrestamo($parametros)
    {
        $this->validarSesion();

        $dni = trim($parametros->dni ?? '');
        $codigoLibro = trim($parametros->codigoLibro ?? '');

        if ($dni === '' || $codigoLibro === '') {
            throw new SoapFault("Client", "Debe indicar dni y codigoLibro.");
        }

        // Lógica de ejemplo
        if ($codigoLibro === "L001") {
            return [
                "puede_prestar" => true,
                "mensaje" => "Préstamo autorizado para el usuario con DNI $dni.",
                "dias_maximos" => 15
            ];
        } elseif ($codigoLibro === "L999") {
            return [
                "puede_prestar" => false,
                "mensaje" => "El libro no está disponible.",
                "dias_maximos" => 0
            ];
        } else {
            return [
                "puede_prestar" => true,
                "mensaje" => "Consulta correcta. Libro disponible.",
                "dias_maximos" => 7
            ];
        }
    }
}

/*
|--------------------------------------------------------------------------
| MODO 1: SI ES PETICIÓN DEL FORMULARIO HTML -> ACTÚA COMO CLIENTE SOAP
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dni']) && isset($_POST['codigoLibro'])) {
    $dni = $_POST['dni'];
    $codigoLibro = $_POST['codigoLibro'];

    try {
        $cliente = new SoapClient($wsdl, [
            'trace' => 1,
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE
        ]);

        // Cabecera SOAP de sesión
        $datosSesion = [
            'idSesion' => '12345',
            'usuario'  => 'admin'
        ];

        $header = new SoapHeader(
            'urn:SesionBiblioteca',
            'SessionHeader',
            $datosSesion,
            false
        );

        $cliente->__setSoapHeaders($header);

        $respuesta = $cliente->consultarPrestamo([
            'dni' => $dni,
            'codigoLibro' => $codigoLibro
        ]);

        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Resultado préstamo</title>
            <style>
                body{
                    font-family: Arial, sans-serif;
                    max-width: 900px;
                    margin: 40px auto;
                }
                .caja{
                    border: 1px solid #ccc;
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 20px;
                }
                pre{
                    background: #f4f4f4;
                    padding: 12px;
                    overflow: auto;
                }
            </style>
        </head>
        <body>
            <h1>Resultado de la consulta</h1>

            <div class="caja">
                <p><strong>¿Puede prestar?</strong> <?php echo $respuesta->puede_prestar ? 'Sí' : 'No'; ?></p>
                <p><strong>Mensaje:</strong> <?php echo htmlspecialchars($respuesta->mensaje); ?></p>
                <p><strong>Días máximos:</strong> <?php echo htmlspecialchars($respuesta->dias_maximos); ?></p>
            </div>

            <div class="caja">
                <h2>Última petición SOAP</h2>
                <pre><?php echo htmlspecialchars($cliente->__getLastRequest()); ?></pre>
            </div>

            <div class="caja">
                <h2>Última respuesta SOAP</h2>
                <pre><?php echo htmlspecialchars($cliente->__getLastResponse()); ?></pre>
            </div>

            <p><a href="cliente.html">Volver</a></p>
        </body>
        </html>
        <?php

    } catch (SoapFault $e) {
        echo "<h2>Error SOAP</h2>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo '<p><a href="cliente.html">Volver</a></p>';
    }

    exit;
}

/*
|--------------------------------------------------------------------------
| MODO 2: SI ES PETICIÓN SOAP -> ACTÚA COMO SERVIDOR SOAP
|--------------------------------------------------------------------------
*/
$sesion = obtenerCabeceraSesionDesdeXML();

$server = new SoapServer($wsdl, [
    'cache_wsdl' => WSDL_CACHE_NONE
]);

$server->setObject(new ServicioBiblioteca($sesion));
$server->handle();
?>