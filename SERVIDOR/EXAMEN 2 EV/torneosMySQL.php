<?php

$host = "127.0.0.1";
$port = "3306";
$dbname = "videojuegos_asir";
$user = "root";
$pass = "";

try {
    // TODO 1:
    // Crear la conexión PDO con MySQL usando el host, el puerto, el nombre de la base de datos,
    // el usuario y la contraseña indicados arriba.
    // Después, configurar la conexión para que trabaje en modo excepción cuando se produzca un error.

        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // -------------------------------------------------
    // CREAR TABLA SI NO EXISTE
    // -------------------------------------------------
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

    // =================================================
    // MANEJO DE PETICIONES
    // =================================================
    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        header("Content-Type: application/json; charset=utf-8");

        // TODO 2:
        // Leer el contenido bruto que llega en la petición HTTP.
        // Después, convertir ese contenido desde JSON a un array asociativo de PHP ($datos). 
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

        // ---------------------------------------------
        // INSERTAR
        // ---------------------------------------------
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
                // TODO 3:
                // Preparar una sentencia SQL de inserción para guardar un torneo en la tabla.
                // La sentencia debe incluir marcadores para nombre del torneo, videojuego, plataforma,
                // modalidad, máximo de participantes y premio.

                $sqlInsert = "INSERT INTO torneo (nombre_torneo, videojuego, plataforma, modalidad, max_participantes, premio) 
                              VALUES (:nombre_torneo, :videojuego, :plataforma, :modalidad, :max_participantes, :premio)";
                $stmt = $pdo->prepare($sqlInsert);
                
            
                // TODO 4:
                // Ejecutar la sentencia preparada enviando los valores recogidos del JSON.
                // El máximo de participantes debe tratarse como número entero.

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

        // ---------------------------------------------
        // LISTAR COMPLETO
        // ---------------------------------------------
        if ($accion === "listar") {

            // TODO 6:
            // Ejecutar una consulta que recupere todos los torneos ordenados por id.
                $sqlSelect = "SELECT * FROM torneo ORDER BY id_torneo";
                $stmt = $pdo->query($sqlSelect);



            // TODO 7:
            // Obtener todos los resultados de la consulta en un array asociativo ("$torneos").
                $torneos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                

            echo json_encode([
                "ok" => true,
                "mensaje" => "Listado completo de torneos.",
                "lista_completa" => $torneos
            ]);

            exit;
        }

        // ---------------------------------------------
        // ACTUALIZAR UN DATO MEDIANTE TRANSACCIÓN
        // ---------------------------------------------
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

                    // TODO 9:
                    // Iniciar una transacción antes de realizar la actualización.
                    $pdo->beginTransaction();

                    $sqlUpdate = "UPDATE torneo SET $campo = :valor WHERE id_torneo = :id_torneo";

                    // TODO 10:
                    // Preparar la sentencia SQL de actualización usando la variable anterior.
                    // Debe actualizar únicamente el campo permitido indicado y filtrar por id del torneo.
                    $stmt = $pdo->prepare($sqlUpdate);


                    if ($campo === "max_participantes") {
                        $valor = (int)$valor;
                    }

                    // TODO 11:
                    // Ejecutar la sentencia preparada enviando el nuevo valor y el id del torneo.
                    $stmt->execute([
                        ":valor" => $valor,
                        ":id_torneo" => $idTorneo
                    ]);


                    // TODO 12:
                    // Ejecutar una consulta que recupere todos los torneos ordenados por id,
                    // para mostrar el estado completo de la tabla después de la actualización.
                    $sqlSelect = "SELECT * FROM torneo ORDER BY id_torneo";
                    $stmt = $pdo->query($sqlSelect);


                    // TODO 13:
                    // Obtener todos los resultados de la consulta completa en un array asociativo.
                    $torneos = $stmt->fetchAll(PDO::FETCH_ASSOC);


                    // TODO 14:
                    // Confirmar la transacción para guardar definitivamente los cambios.
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
        // TODO 16:
        // Cancelar la transacción actual para deshacer los cambios si se ha producido un error.
        $pdo->rollBack();
        
    }

    header("Content-Type: application/json; charset=utf-8");
    echo json_encode([
        "ok" => false,
        "mensaje" => "Error en MySQL: " . $e->getMessage()
    ]);
}
?>