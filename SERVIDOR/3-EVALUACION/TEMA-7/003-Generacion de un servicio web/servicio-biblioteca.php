<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

$wsdl = "http://localhost/TU_CARPETA/servicio-biblioteca.wsdl";

/*
    Leer cabecera SOAP manualmente
*/
function obtenerCabeceraSesion()
{
    $xml = file_get_contents("php://input");

    if (!$xml) {
        return null;
    }

    $doc = new DOMDocument();
    $doc->loadXML($xml);

    $xpath = new DOMXPath($doc);

    $xpath->registerNamespace(
        'soap',
        'http://schemas.xmlsoap.org/soap/envelope/'
    );

    $xpath->registerNamespace(
        'ses',
        'urn:SesionBiblioteca'
    );

    $idSesion = $xpath->query(
        '//soap:Header/ses:SessionHeader/ses:idSesion'
    )->item(0);

    $usuario = $xpath->query(
        '//soap:Header/ses:SessionHeader/ses:usuario'
    )->item(0);

    if (!$idSesion || !$usuario) {
        return null;
    }

    return [
        "idSesion" => $idSesion->nodeValue,
        "usuario" => $usuario->nodeValue
    ];
}

$sesion = obtenerCabeceraSesion();

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
            throw new SoapFault(
                "Client",
                "No se recibió cabecera de sesión"
            );
        }

        if (
            $this->sesion["usuario"] != "admin" ||
            $this->sesion["idSesion"] != "12345"
        ) {
            throw new SoapFault(
                "Client",
                "Sesión inválida"
            );
        }
    }

    public function consultarPrestamo($datos)
    {
        $this->validarSesion();

        $dni = $datos->dni;
        $codigoLibro = $datos->codigoLibro;

        if ($codigoLibro == "L001") {
            return [
                "puede_prestar" => true,
                "mensaje" => "Préstamo autorizado",
                "dias_maximos" => 15
            ];
        } else {
            return [
                "puede_prestar" => false,
                "mensaje" => "Libro no disponible",
                "dias_maximos" => 0
            ];
        }
    }
}

$server = new SoapServer($wsdl);
$server->setObject(new ServicioBiblioteca($sesion));
$server->handle();

?>