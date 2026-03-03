<?php

$host = "127.0.0.1";
$port = "3306";
$dbname = "videojuegos_asir";
$user = "root";
$pass = "";

$mensaje = "";
$desarrolladores = []

try {

    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SE

?>