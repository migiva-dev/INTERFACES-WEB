<?php

require 'vendor/autoload.php';

use MongoDB\Client;


$client = new Client("mongodb://localhost:27017");


$baseDatos = $client->Videojuegos; 
$coleccion = $baseDatos->participantes_torneo;


if ($_SERVER["REQUEST_METHOD"] === "GET") {

   
    $accion = $_GET["accion"] ?? "";


    if ($accion === "consultar_cookie") {

        header("Content-Type: application/json; charset=utf-8");

  
        $nombreParticipante = trim($_COOKIE["nombreParticipanteConsulta"] ?? "");

        if ($nombreParticipante !== "") {

      
    
            $documento = $coleccion->findOne([
                "nombreParticipante" => $nombreParticipante
            ]);


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

$contenido = file_get_contents("php://input");
$datos = json_decode($contenido, true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $accion = $_POST["accion"] ?? "";

    if ($accion === "") {
        if (is_array($datos)) {
            $accion = $datos["accion"] ?? "";
        }
    }

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
        if (!empty($documento)) {


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


    if ($accion === "eliminar") {

        $nombreBuscado = trim($_POST["nombreParticipanteBusqueda"] ?? "");

        if ($nombreBuscado !== "") {

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