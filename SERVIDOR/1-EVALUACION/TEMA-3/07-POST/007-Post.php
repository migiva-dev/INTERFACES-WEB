<?php
// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos enviados por POST
    $nombre = $_POST['nombre'];
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    echo '<div class="output">';
    echo "<h2>Datos recibidos:</h2>";
    echo "<p><strong>Nombre:</strong> " . htmlspecialchars($nombre) . "</p>";
    if (!empty($email)) {
        echo "<p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>";
    }
    echo '</div>';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title></title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-image: url('https://static.eldiario.es/clip/d691c859-cf7e-4136-9a2f-d1c71b9070d1_16-9-discover-aspect-ratio_default_0.jpg'); /* <-- Cambia esta URL por la imagen que quieras */
            background-repeat: no-repeat;
            background-size: cover;   /* Hace que la imagen ocupe toda la pantalla */
            background-position: center;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
  
  
</head>
<body>
    <div class="container">
        <h1>Formulario de ejemplo usando POST</h1>
        <form method="post" action="">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email">

            <input type="submit" value="Enviar">
        </form>
    </div>
</body>
</html>
