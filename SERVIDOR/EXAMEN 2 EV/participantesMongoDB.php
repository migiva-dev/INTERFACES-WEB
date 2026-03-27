<?php

require 'vendor/autoload.php';

use MongoDB\Client;

$client = new Client("mongodb://localhost:27017");

// TODO 1:
// Seleccionar la base de datos y la colección con las que se va a trabajar.
// La base de datos debe ser la de videojuegos ("Videojuegos") y la colección la de participantes del torneo ("participantes_torneo").

// =====================================================
// 1) CONSULTAR PARTICIPANTE MEDIANTE COOKIE
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $accion = $_GET["accion"] ?? "";

    if ($accion === "consultar_cookie") {

        header("Content-Type: application/json; charset=utf-8");

        $nombreParticipante = trim($_COOKIE["nombreParticipanteConsulta"] ?? "");

        if ($nombreParticipante !== "") {

            // TODO 2:
            // Buscar en la colección un único documento cuyo nombre de participante
            // coincida exactamente con el valor recibido en la cookie.

            if ($documento !== null) {

                $participante = [
                    "_id" => isset($documento["_id"]) ? (string)$documento["_id"] : "",
                    "torneoInscripcion" => $documento["torneoInscripcion"] ?? "",
                    "nombreParticipante" => $documento["nombreParticipante"] ?? "",
                    "nick" => $documento["nick"] ?? "",
                    "email" => $documento["email"] ?? "",
                    "edad" => $documento["edad"] ?? "",
                    "nombreEquipo" => $documento["nombreEquipo"] ?? "",
                    "telefono" => $documento["telefono"] ?? ""
                ];

                echo json_encode([
                    "ok" => true,
                    "mensaje" => "Participante encontrado.",
                    "participante" => $participante
                ]);

            } else {
                echo json_encode([
                    "ok" => false,
                    "mensaje" => "No se encontró ningún participante con ese nombre."
                ]);
            }

        } else {
            echo json_encode([
                "ok" => false,
                "mensaje" => "No se ha recibido ninguna cookie con el nombre del participante."
            ]);
        }
    }

    exit;
}

// =====================================================
// 2) AÑADIR PARTICIPANTE MEDIANTE POST + JSON
// 3) ELIMINAR PARTICIPANTE MEDIANTE POST SIMPLE
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $accion = $_POST["accion"] ?? "";

    // -------------------------------------------------
    // INSERTAR PARTICIPANTE
    // -------------------------------------------------
    // Como el insert se envía en JSON, $_POST["accion"] no llegará.
    // Entonces leemos el cuerpo de la petición y extraemos la acción.
    if ($accion === "") {

        // TODO 3:
        // Leer el contenido bruto que llega en la petición HTTP.
        // Después, convertir ese contenido desde JSON a un array asociativo de PHP ("$datos").

        if (is_array($datos)) {
            $accion = $datos["accion"] ?? "";
        }
    }

    // -------------------------------------------------
    // INSERTAR CON JSON
    // -------------------------------------------------
    if ($accion === "insertar") {

        header("Content-Type: application/json; charset=utf-8");

        if (!isset($datos) || !is_array($datos)) {
            $contenido = file_get_contents("php://input");
            $datos = json_decode($contenido, true);
        }

        if (!is_array($datos)) {
            echo json_encode([
                "ok" => false,
                "mensaje" => "No se ha recibido un JSON válido."
            ]);
            exit;
        }

        $torneoInscripcion = trim($datos["torneoInscripcion"] ?? "");
        $nombreParticipante = trim($datos["nombreParticipante"] ?? "");
        $nick = trim($datos["nick"] ?? "");
        $email = trim($datos["email"] ?? "");
        $edad = $datos["edad"] ?? "";
        $nombreEquipo = trim($datos["nombreEquipo"] ?? "");
        $telefono = trim($datos["telefono"] ?? "");

        $documento = [];

        // TODO 4:
        // Construir el documento que se va a insertar en MongoDB.
        // Añadir solo los campos que no estén vacíos.
        // Incluir torneo, nombre del participante, nick, email, edad, nombre del equipo y teléfono.

        if (!empty($documento)) {

            // TODO 5:
            // Insertar en la colección el documento construido anteriormente
            // y guardar el resultado de la operación para poder recuperar el id generado.

            $participanteInsertado = [
                "_id" => (string)$resultado->getInsertedId(),
                "torneoInscripcion" => $documento["torneoInscripcion"] ?? "",
                "nombreParticipante" => $documento["nombreParticipante"] ?? "",
                "nick" => $documento["nick"] ?? "",
                "email" => $documento["email"] ?? "",
                "edad" => $documento["edad"] ?? "",
                "nombreEquipo" => $documento["nombreEquipo"] ?? "",
                "telefono" => $documento["telefono"] ?? ""
            ];

            echo json_encode([
                "ok" => true,
                "mensaje" => "Participante insertado correctamente.",
                "participante_insertado" => $participanteInsertado
            ]);

        } else {
            echo json_encode([
                "ok" => false,
                "mensaje" => "No se ha insertado nada porque todos los campos estaban vacíos."
            ]);
        }

        exit;
    }

    // -------------------------------------------------
    // ELIMINAR CON POST SIMPLE
    // -------------------------------------------------
    if ($accion === "eliminar") {

        $nombreBuscado = trim($_POST["nombreParticipanteBusqueda"] ?? "");

        if ($nombreBuscado !== "") {

            // TODO 6:
            // Eliminar de la colección un único documento cuyo nombre de participante
            // coincida exactamente con el nombre recibido en el formulario.

            if ($resultado->getDeletedCount() > 0) {
                echo "Participante eliminado correctamente.";
            } else {
                echo "No se encontró ningún participante con ese nombre.";
            }

        } else {
            echo "Debes indicar el nombre del participante que quieres eliminar.";
        }

        exit;
    }

    echo "Acción POST no válida.";
}
?>