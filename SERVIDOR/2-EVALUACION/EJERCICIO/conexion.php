<?php
$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "formulario_validaciones";

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8");

$accion = $_POST["accion"] ?? "";

if ($accion === "insertar") {
    $nombre = trim($_POST["nombre"] ?? "");
    $dni = trim($_POST["dni"] ?? "");
    $nie = trim($_POST["nie"] ?? "");
    $matricula = trim($_POST["matricula"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $descripcion = trim($_POST["descripcion"] ?? "");
    $fechaNacimiento = trim($_POST["fechaNacimiento"] ?? "");

    $sql = "INSERT INTO usuarios (nombre, dni, nie, matricula, password, descripcion, fecha_nacimiento)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la consulta: " . $conn->error);
    }

    $stmt->bind_param("sssssss", $nombre, $dni, $nie, $matricula, $password, $descripcion, $fechaNacimiento);

    if ($stmt->execute()) {
        echo "Usuario insertado correctamente en la base de datos.";
    } else {
        echo "Error al insertar: " . $stmt->error;
    }

    $stmt->close();
}

elseif ($accion === "buscar") {
    $nombre = trim($_POST["nombre"] ?? "");

    $sql = "SELECT id, nombre, dni, nie, matricula, descripcion, fecha_nacimiento, created_at
            FROM usuarios
            WHERE nombre = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $nombre);
    $stmt->execute();

    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();

        echo "Usuario encontrado:\n";
        echo "ID: " . $fila["id"] . "\n";
        echo "Nombre: " . $fila["nombre"] . "\n";
        echo "DNI: " . $fila["dni"] . "\n";
        echo "NIE: " . $fila["nie"] . "\n";
        echo "Matrícula: " . $fila["matricula"] . "\n";
        echo "Descripción: " . $fila["descripcion"] . "\n";
        echo "Fecha de nacimiento: " . $fila["fecha_nacimiento"] . "\n";
        echo "Creado: " . $fila["created_at"];
    } else {
        echo "No se encontró ningún usuario con ese nombre.";
    }

    $stmt->close();
}

elseif ($accion === "actualizar") {
    $nombre = trim($_POST["nombre"] ?? "");
    $nuevaDescripcion = trim($_POST["descripcion"] ?? "");

    if ($nombre === "" || $nuevaDescripcion === "") {
        die("Error: Debes indicar nombre y nueva descripción.");
    }

    try {
        $conn->begin_transaction();

        $sqlUpdate = "UPDATE usuarios SET descripcion = ? WHERE nombre = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);

        if (!$stmtUpdate) {
            throw new Exception("Error en UPDATE: " . $conn->error);
        }

        $stmtUpdate->bind_param("ss", $nuevaDescripcion, $nombre);
        $stmtUpdate->execute();

        if ($stmtUpdate->affected_rows === 0) {
            throw new Exception("No se encontró ninguna fila con ese nombre.");
        }

        $stmtUpdate->close();

        $sqlSelect = "SELECT id, nombre, dni, nie, matricula, descripcion, fecha_nacimiento, created_at
                      FROM usuarios
                      WHERE nombre = ?";

        $stmtSelect = $conn->prepare($sqlSelect);

        if (!$stmtSelect) {
            throw new Exception("Error en SELECT: " . $conn->error);
        }

        $stmtSelect->bind_param("s", $nombre);
        $stmtSelect->execute();

        $resultado = $stmtSelect->get_result();
        $fila = $resultado->fetch_assoc();

        $conn->commit();

        echo "Transacción realizada correctamente.\n";
        echo "Fila modificada:\n";
        echo "ID: " . $fila["id"] . "\n";
        echo "Nombre: " . $fila["nombre"] . "\n";
        echo "DNI: " . $fila["dni"] . "\n";
        echo "NIE: " . $fila["nie"] . "\n";
        echo "Matrícula: " . $fila["matricula"] . "\n";
        echo "Descripción: " . $fila["descripcion"] . "\n";
        echo "Fecha de nacimiento: " . $fila["fecha_nacimiento"] . "\n";
        echo "Creado: " . $fila["created_at"];

        $stmtSelect->close();

    } catch (Exception $e) {
        $conn->rollback();
        echo "Error en la transacción: " . $e->getMessage();
    }
}

elseif ($accion === "eliminar") {
    $nombre = trim($_POST["nombre"] ?? "");

    $sql = "DELETE FROM usuarios WHERE nombre = ?";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Error en la consulta: " . $conn->error);
    }

    $stmt->bind_param("s", $nombre);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Usuario eliminado correctamente.";
        } else {
            echo "No existe ningún usuario con ese nombre.";
        }
    } else {
        echo "Error al eliminar: " . $stmt->error;
    }

    $stmt->close();
}

else {
    echo "Error: acción no válida.";
}

$conn->close();
?>