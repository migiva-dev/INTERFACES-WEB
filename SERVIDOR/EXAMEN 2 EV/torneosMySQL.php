<?php

$host = "127.0.0.1";
$port = "3306";
$dbname = "videojuegos_asir";
$user = "root";
$pass = "";

try {


        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sqlCrearTabla = "
        CREATE TABLE IF NOT EXISTS torneo (
            id_torneo INT AUTO_INCREMENT PRIMARY KEY,
            nombre_torneo VARCHAR(100) NOT NULL,
            videojuego VARCHAR(80) NOT NULL,
            plataforma VARCHAR(50) NOT NULL,
            modalidad VARCHAR(30) NOT NULL,
            max_participantes INT NOT NULL,
            premio VARCHAR(120) DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ";

    $pdo->exec($sqlCrearTabla);


    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        header("Content-Type: application/json; charset=utf-8");

            $contenido = file_get_contents("php://input");
            $datos = json_decode($contenido, true);
        

        if (!is_array($datos)) {
            echo json_encode([
                "ok" => false,
                "mensaje" => "No se ha recibido un JSON válido."
            ]);
            exit;
        }

        $accion = $datos["accion"] ?? "";


        if ($accion === "insertar") {

            $nombreTorneo = trim($datos["nombre_torneo"] ?? "");
            $videojuego = trim($datos["videojuego"] ?? "");
            $plataforma = trim($datos["plataforma"] ?? "");
            $modalidad = trim($datos["modalidad"] ?? "");
            $maxParticipantes = $datos["max_participantes"] ?? "";
            $premio = trim($datos["premio"] ?? "");

            if (
                $nombreTorneo !== "" &&
                $videojuego !== "" &&
                $plataforma !== "" &&
                $modalidad !== "" &&
                $maxParticipantes !== ""
            ) {
       

                $sqlInsert = "INSERT INTO torneo (nombre_torneo, videojuego, plataforma, modalidad, max_participantes, premio) 
                              VALUES (:nombre_torneo, :videojuego, :plataforma, :modalidad, :max_participantes, :premio)";
                $stmt = $pdo->prepare($sqlInsert);
                


                $stmt->execute([
                    ":nombre_torneo" => $nombreTorneo,
                    ":videojuego" => $videojuego,
                    ":plataforma" => $plataforma,
                    ":modalidad" => $modalidad,
                    ":max_participantes" => (int)$maxParticipantes,
                    ":premio" => $premio,
                ]);

                echo json_encode([
                    "ok" => true,
                    "mensaje" => "Torneo insertado correctamente.",
                    "torneo_insertado" => [
                        "id_torneo" => $pdo->lastInsertId(),
                        "nombre_torneo" => $nombreTorneo,
                        "videojuego" => $videojuego,
                        "plataforma" => $plataforma,
                        "modalidad" => $modalidad,
                        "max_participantes" => (int)$maxParticipantes,
                        "premio" => $premio
                    ]
                ]);

            } else {
                echo json_encode([
                    "ok" => false,
                    "mensaje" => "Faltan datos obligatorios para insertar el torneo."
                ]);
            }

            exit;
        }

        if ($accion === "listar") {


                $sqlSelect = "SELECT * FROM torneo ORDER BY id_torneo";
                $stmt = $pdo->query($sqlSelect);

                $torneos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                

            echo json_encode([
                "ok" => true,
                "mensaje" => "Listado completo de torneos.",
                "lista_completa" => $torneos
            ]);

            exit;
        }
        if ($accion === "actualizar") {

            $idTorneo = $datos["id_torneo"] ?? "";
            $campo = trim($datos["campo"] ?? "");
            $valor = $datos["valor"] ?? "";

            $camposPermitidos = [
                "nombre_torneo",
                "videojuego",
                "plataforma",
                "modalidad",
                "max_participantes",
                "premio"
            ];

            if ($idTorneo !== "" && $campo !== "" && $valor !== "") {

                if (in_array($campo, $camposPermitidos)) {

                    $pdo->beginTransaction();

                    $sqlUpdate = "UPDATE torneo SET $campo = :valor WHERE id_torneo = :id_torneo";

         
                    $stmt = $pdo->prepare($sqlUpdate);


                    if ($campo === "max_participantes") {
                        $valor = (int)$valor;
                    }

       
                    $stmt->execute([
                        ":valor" => $valor,
                        ":id_torneo" => $idTorneo
                    ]);

                    $sqlSelect = "SELECT * FROM torneo ORDER BY id_torneo";
                    $stmt = $pdo->query($sqlSelect);


                    $torneos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $pdo->commit();

                    echo json_encode([
                        "ok" => true,
                        "mensaje" => "Torneo actualizado correctamente.",
                        "lista_completa" => $torneos
                    ]);

                } else {
                    echo json_encode([
                        "ok" => false,
                        "mensaje" => "El campo indicado no se puede actualizar."
                    ]);
                }

            } else {
                echo json_encode([
                    "ok" => false,
                    "mensaje" => "Faltan datos para actualizar el torneo."
                ]);
            }

            exit;
        }

        echo json_encode([
            "ok" => false,
            "mensaje" => "Acción POST no válida."
        ]);
        exit;
    }

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "ok" => false,
        "mensaje" => "Método no permitido."
    ]);

} catch (PDOException $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
 
        $pdo->rollBack();
        
    }

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "ok" => false,
        "mensaje" => "Error en MySQL: " . $e->getMessage()
    ]);
}
?>