<?php
include("conexion.php");

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
$conn->close();
?>