<?php
header("Content-Type: application/json; charset=utf-8");

$host = "127.0.0.1";
$port = "3307";
$dbname = "videojuegos_asir";
$user = "root";
$pass = "";

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

$accion = $data["accion"] ?? "";

try {
    // TODO 1:
    // Crear la conexión PDO con MySQL
    $pdo = new PDO (
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  



    
    // NO TOCAR: Creación de la tabla favorito
    $sqlCrearTabla = "
        CREATE TABLE IF NOT EXISTS favorito (
            id_favorito INT NOT NULL AUTO_INCREMENT,
            titulo VARCHAR(160) NOT NULL,
            fecha_lanzamiento DATE DEFAULT NULL,
            precio_base DECIMAL(8,2) DEFAULT NULL,
            guardado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_favorito)
        )
    ";

    $pdo->exec($sqlCrearTabla);

    if ($accion === "guardar_favorito") {
        $juego = $data["juego"] ?? null;

        if ($juego !== null) {
            // TODO 2:
            // Preparar una consulta INSERT segura para insertar un favorito.
            //
            // Debe insertar estos campos:
            // - titulo
            // - fecha_lanzamiento
            // - precio_base
            //
            // Usa parámetros con nombre.
            $sql = "INSERT INTO favorito (titulo, fecha_lanzamiento, precio_base)
                    VALUES (:titulo, :fecha_lanzamiento, :precio_base)";
            $stmt = $pdo->prepare($sql);

            // TODO 3:
            // Ejecutar la consulta preparada enviando los valores del juego recibido en el JSON.
            $stmt->execute([
                ":titulo" => $juego["titulo"] ?? null,
                ":fecha_lanzamiento" => $juego["fecha_lanzamiento"] ?? null,
                ":precio_base" => $juego["precio_base"] ?? null
            ]);

            echo json_encode([
                "ok" => true,
                "mensaje" => "Favorito guardado correctamente en MySQL."
            ]);
            exit;
        }

        echo json_encode([
            "ok" => false,
            "mensaje" => "No se recibió ningún juego."
        ]);
        exit;
    }

    if ($accion === "listar_favoritos") {
        // TODO 4:
        // Ejecutar una consulta SELECT para obtener todos los favoritos.
        //
        // Deben recuperarse estos campos:
        // - id_favorito
        // - titulo
        // - fecha_lanzamiento
        // - precio_base
        // - guardado_en
        $sql = "SELECT id_favorito, titulo, fecha_lanzamiento, precio_base, guardado_en
                FROM favorito";
        $stmt = $pdo->query($sql);

        $favoritos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "ok" => true,
            "total" => count($favoritos),
            "favoritos" => $favoritos
        ], JSON_PRETTY_PRINT);
        exit;
    }

    if ($accion === "eliminar_favorito") {
        $idFavorito = $data["id_favorito"] ?? "";

        // TODO 5:
        // Preparar una consulta DELETE segura que elimine un favorito
        // filtrando por su id_favorito.
        //
        // Debe usarse un parámetro con nombre.
        $sql = "DELETE FROM favorito WHERE id_favorito = :id_favorito";
        $stmt = $pdo->prepare($sql);

        // TODO 6:
        // Ejecutar la consulta preparada enviando el id_favorito recibido
        // en el JSON de entrada.

        $stmt->execute([
            ":id_favorito" => $idFavorito
        ]);

        echo json_encode([
            "ok" => true,
            "mensaje" => "Favoritos eliminados: " . $stmt->rowCount()
        ]);
        exit;
    }

    echo json_encode([
        "ok" => false,
        "mensaje" => "Acción no reconocida."
    ]);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "ok" => false,
        "error" => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}