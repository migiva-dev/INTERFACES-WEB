<?php
// supermercado.php
// Recibe JSON por POST y devuelve JSON. También gestiona cookie "ingresos".

header("Content-Type: application/json; charset=utf-8");

// Leer JSON de entrada

// 1) Lee el cuerpo de la petición HTTP (POST) que llega en formato JSON y guarda el contenido en una variable $raw.
$raw = file_get_contents("php://input");

// 2) Convierte el JSON recibido a un array asociativo llamado $data.
$data = json_decode($raw, true);

// Si el JSON no es válido o viene vacío
if (!is_array($data)) {
  echo json_encode(["error" => "JSON inválido"], JSON_UNESCAPED_UNICODE);
  exit;
}

// 3) Guarda en una variable $accion la accion recibida
$accion = $data["accion"] ?? null;


// Lista simple de precios (€/unidad)
$precios = [
  "Pan"   => 1.20,
  "Leche" => 0.95,
  "Huevos"=> 2.40,
  "Arroz" => 1.10,
  "Café"  => 3.50
];

if ($accion === "calcular_precio") {
  // 1) Obtén el producto a partir del array $data y guardalo en una variable $item.
  $item = $data["item"] ?? null;

  // 2) Obtén la cantidad a partir del array $data y guardala en una variable $cantidad.
  $cantidad = $data["cantidad"] ?? null;

  if (!$item || !isset($precios[$item])) {
    echo json_encode(["error" => "Producto no encontrado"], JSON_UNESCAPED_UNICODE);
    exit;
  }

  // Validar cantidad
  if (!is_numeric($cantidad) || (int)$cantidad < 1) {
    echo json_encode(["error" => "Cantidad inválida"], JSON_UNESCAPED_UNICODE);
    exit;
  }
  $cantidad = (int)$cantidad;

  // 3) Calcula el precio total
  $precio_unitario = (float)$precios[$item];
  $precio = round($precio_unitario * $cantidad, 2);

  // 4) Devuelve una respuesta JSON
  echo json_encode([
    "item" => $item,
    "cantidad" => $cantidad,
    "precio_unitario" => $precio_unitario,
    "precio" => $precio
  ], JSON_UNESCAPED_UNICODE);

  exit;
}

    if ($accion === "sumar_ingresos") {
        
    // TODO:
    // 1) Guarda el importe enviado desde el array $data en $importe.

    $importe = $data["importe"] ?? 0;

    if (!is_numeric($importe)) {
        echo json_encode(["error" => "Importe inválido"], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $importe = (float)$importe;

    // // TODO:
    // 2) Comprueba si existe la cookie "ingresos":
    //    - Si existe, guardala en una variable $actual.
    //    - Si no existe, asigna 0 a $actual.
    if (isset($_COOKIE["ingresos"]) && is_numeric($_COOKIE["ingresos"])) {
        $actual = (float)$_COOKIE["ingresos"];
    } else {
        $actual = 0.0;
    }

    // TODO:
    // 3) Calcula el nuevo total de ingresos:
    //    - $nuevo = $actual + $importe

    $nuevo = round($actual + $importe, 2);

    // TODO:
    // 4) Guarda el nuevo valor en la cookie "ingresos"
    //    - Duración: 30 días
    //    - Ruta: "/"

    setcookie("ingresos", (string)$nuevo, time() + (30 * 24 * 60 * 60), "/");


    // TODO:
    // 5) Devuelve una respuesta JSON usando echo json_encode(...) con:
    //    - "ok" => true
    //    - "ingresos" => $nuevo
    //    y finalizar con exit;

    echo json_encode([
        "ok" => true,
        "ingresos" => $nuevo
    ], JSON_UNESCAPED_UNICODE);

    exit;
    
}

echo json_encode(["error" => "Acción no válida"], JSON_UNESCAPED_UNICODE);
exit;
