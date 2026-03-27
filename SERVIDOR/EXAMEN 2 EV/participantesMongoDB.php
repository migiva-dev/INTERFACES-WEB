<?php

require 'vendor/autoload.php';

use MongoDB\Client;

// Crear conexión con MongoDB
$client = new Client("mongodb://localhost:27017");

// =====================================================
// TODO 1: Seleccionar base de datos y colección
// =====================================================
$baseDatos = $client->Videojuegos; // Base de datos
$coleccion = $baseDatos->participantes_torneo; // Colección

// =====================================================
// 1) CONSULTAR PARTICIPANTE MEDIANTE COOKIE (GET)
// =====================================================
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Se recoge la acción desde la URL
    $accion = $_GET["accion"] ?? "";

    // Solo se ejecuta si la acción es consultar por cookie
    if ($accion === "consultar_cookie") {

        header("Content-Type: application/json; charset=utf-8");

        // Leer el nombre del participante desde la cookie
        $nombreParticipante = trim($_COOKIE["nombreParticipanteConsulta"] ?? "");

        if ($nombreParticipante !== "") {

            // =====================================================
            // TODO 2: Buscar participante por nombre
            // =====================================================
            $documento = $coleccion->findOne([
                "nombreParticipante" => $nombreParticipante
            ]);

            // Si se encuentra el documento
            if ($documento !== null) {

                // Se prepara la respuesta
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
                // No encontrado
                echo json_encode([
                    "ok" => false,
                    "mensaje" => "No se encontró ningún participante con ese nombre."
                ]);
            }

        } else {
            // No hay cookie
            echo json_encode([
                "ok" => false,
                "mensaje" => "No se ha recibido ninguna cookie con el nombre del participante."
            ]);
        }
    }

    exit;
}

// =====================================================
// 2) AÑADIR PARTICIPANTE (POST + JSON)
// 3) ELIMINAR PARTICIPANTE (POST simple)
// =====================================================


$contenido = file_get_contents("php://input");
$datos = json_decode($contenido, true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Intentar obtener acción desde POST (formulario clásico)
    $accion = $_POST["accion"] ?? "";

    // -------------------------------------------------
    // Si viene JSON, la acción no estará en $_POST
    // -------------------------------------------------
    if ($accion === "") {
        if (is_array($datos)) {
            $accion = $datos["accion"] ?? "";
        }
    }

    // =====================================================
    // INSERTAR PARTICIPANTE (JSON)
    // =====================================================
    if ($accion === "insertar") {

        header("Content-Type: application/json; charset=utf-8");

        // Validar JSON
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

        // Recoger datos
        $torneoInscripcion = trim($datos["torneoInscripcion"] ?? "");
        $nombreParticipante = trim($datos["nombreParticipante"] ?? "");
        $nick = trim($datos["nick"] ?? "");
        $email = trim($datos["email"] ?? "");
        $edad = $datos["edad"] ?? "";
        $nombreEquipo = trim($datos["nombreEquipo"] ?? "");
        $telefono = trim($datos["telefono"] ?? "");

        $documento = [];

        // =====================================================
        // TODO 4: Construir documento dinámicamente
        // =====================================================
        if ($torneoInscripcion !== "") {
            $documento["torneoInscripcion"] = $torneoInscripcion;
        }

        if ($nombreParticipante !== "") {
            $documento["nombreParticipante"] = $nombreParticipante;
        }

        if ($nick !== "") {
            $documento["nick"] = $nick;
        }

        if ($email !== "") {
            $documento["email"] = $email;
        }

        if ($edad !== "") {
            $documento["edad"] = (int)$edad;
        }

        if ($nombreEquipo !== "") {
            $documento["nombreEquipo"] = $nombreEquipo;
        }

        if ($telefono !== "") {
            $documento["telefono"] = $telefono;
        }

        // Si hay datos para insertar
        if (!empty($documento)) {

            // =====================================================
            // TODO 5: Insertar en MongoDB
            // =====================================================
            $resultado = $coleccion->insertOne($documento);

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

    // =====================================================
    // ELIMINAR PARTICIPANTE (POST simple)
    // =====================================================
    if ($accion === "eliminar") {

        // Obtener nombre desde formulario
        $nombreBuscado = trim($_POST["nombreParticipanteBusqueda"] ?? "");

        if ($nombreBuscado !== "") {

            // =====================================================
            // TODO 6: Eliminar participante
            // =====================================================
            $resultado = $coleccion->deleteOne([
                "nombreParticipante" => $nombreBuscado
            ]);

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