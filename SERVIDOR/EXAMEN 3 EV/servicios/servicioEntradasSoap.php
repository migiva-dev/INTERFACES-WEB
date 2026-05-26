<?php
// =====================================================
// SERVICIO 2: PRECIO DE ENTRADAS MEDIANTE SOAP
// =====================================================
// Operación admitida: calcularPrecioEntradas
// Precio normal: 8 euros
// Precio reducido: 5 euros

function procesarPeticionEntradasSoap()
{
    header("Content-Type: text/xml; charset=utf-8");

    $xmlRecibido = file_get_contents("php://input");

    libxml_use_internal_errors(true);

    $documento = new DOMDocument();

    if (!$documento->loadXML($xmlRecibido)) {
        libxml_clear_errors();

        enviarFaultSoap(
            "La petición recibida no contiene un XML válido."
        );

        return;
    }

    $body = $documento->getElementsByTagNameNS(
        "http://schemas.xmlsoap.org/soap/envelope/",
        "Body"
    )->item(0);

    if ($body === null) {
        enviarFaultSoap(
            "La petición no contiene un Body SOAP."
        );

        return;
    }

    $operacion = obtenerPrimerElementoHijo($body);

    if (
        $operacion === null ||
        $operacion->localName !== "calcularPrecioEntradas"
    ) {
        enviarFaultSoap(
            "La operación SOAP solicitada no existe."
        );

        return;
    }
      // TODO 8:
    // Recuperar de la operación SOAP los parámetros enviados por el cliente.
    // Consultar el WSDL para identificar los nombres de los datos recibidos.
    // Transformar la cantidad a un número entero y guardar el tipo como texto.
    
    //$nodoCantidad

    // $nodoTipo


    $nodoCantidad = obtenerNodoPorNombreLocal($operacion, "cantidad");
    $nodoTipo = obtenerNodoPorNombreLocal($operacion, "tipo");

    if ($nodoCantidad === null || $nodoTipo === null) {
        enviarFaultSoap(
            "Faltan los parámetros cantidad o tipo."
        );

        return;
    }

    $cantidad = (int) $nodoCantidad->textContent;
    $tipo = trim($nodoTipo->textContent);

    if ($cantidad <= 0) {
        enviarFaultSoap(
            "La cantidad de entradas debe ser mayor que cero."
        );

        return;
    }

    // TODO 9:
    // Calcular el precio de las entradas.
    // Una entrada normal cuesta 8 euros y una entrada reducida cuesta 5 euros.
    // Si el tipo recibido no es válido, devolver un error SOAP.
    /*
    enviarFaultSoap(
            "El tipo de entrada debe ser normal o reducida."
        );
    */
    // Después, calcular el precio total en función de la cantidad recibida 
    // utilizando la función enviarRespuestaPrecioSoap().
    $precioUnitario = 0;

    if ($tipo === "normal") {
        $precioUnitario = 8;
    } else if ($tipo === "reducida") {
        $precioUnitario = 5;
    } else {
        enviarFaultSoap(
            "El tipo de entrada debe ser normal o reducida."
        );

        return;
    }

    $precioTotal = $cantidad * $precioUnitario;
    enviarRespuestaPrecioSoap(
        $cantidad,
        $tipo,
        $precioUnitario,
        $precioTotal
    );




    
}

function obtenerPrimerElementoHijo($nodoPadre)
{
    foreach ($nodoPadre->childNodes as $nodo) {
        if ($nodo->nodeType === XML_ELEMENT_NODE) {
            return $nodo;
        }
    }

    return null;
}

function obtenerNodoPorNombreLocal($nodoPadre, $nombre)
{
    foreach (
        $nodoPadre->getElementsByTagNameNS("*", $nombre)
        as $nodo
    ) {
        return $nodo;
    }

    foreach (
        $nodoPadre->getElementsByTagName($nombre)
        as $nodo
    ) {
        return $nodo;
    }

    return null;
}

function enviarRespuestaPrecioSoap(
    $cantidad,
    $tipo,
    $precioUnitario,
    $precioTotal
) {
    // TODO 10:
    // Construir y devolver la respuesta XML SOAP de la operación.
    // Consultar el WSDL para identificar los datos que debe contener la respuesta.

    


    $precioUnitarioFormateado = number_format(
        $precioUnitario,
        2,
        ".",
        ""
    );

    $precioTotalFormateado = number_format(
        $precioTotal,
        2,
        ".",
        ""
    );

    $tipoSeguro = htmlspecialchars(
        $tipo,
        ENT_XML1 | ENT_QUOTES,
        "UTF-8"
    );

    // echo
    echo '<?xml version="1.0" encoding="UTF-8"?>' .
        '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">' .
            '<soap:Body>' .
                '<calcularPrecioEntradasResponse>' .
                    '<cantidad>' . $cantidad . '</cantidad>' .
                    '<tipo>' . $tipoSeguro . '</tipo>' .
                    '<precioUnitario>' . $precioUnitarioFormateado . '</precioUnitario>' .
                    '<precioTotal>' . $precioTotalFormateado . '</precioTotal>' .
                '</calcularPrecioEntradasResponse>' .
            '</soap:Body>' .
        '</soap:Envelope>';


}

function enviarFaultSoap($mensaje)
{
    http_response_code(500);

    header("Content-Type: text/xml; charset=utf-8");

    $mensajeSeguro = htmlspecialchars(
        $mensaje,
        ENT_XML1 | ENT_QUOTES,
        "UTF-8"
    );

    echo '<?xml version="1.0" encoding="UTF-8"?>' .
        '<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">' .
            '<soap:Body>' .
                '<soap:Fault>' .
                    '<faultcode>soap:Client</faultcode>' .
                    '<faultstring>' . $mensajeSeguro . '</faultstring>' .
                '</soap:Fault>' .
            '</soap:Body>' .
        '</soap:Envelope>';
}
