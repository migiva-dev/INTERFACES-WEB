<?php
header("Content-Type: application/json; charset=utf-8");

// --- CONFIG DB (ajusta si procede) ---
$host = "127.0.0.1";
$port = "3306";
$dbname = "videojuegos_asir";
$user = "root";
$pass = "";

// --- Leer JSON del body ---
$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

// Si el JSON viene mal
if (!is_array($data)) {
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "JSON inválido"
  ], JSON_PRETTY_PRINT);
  exit;
}

$accion = $data["accion"] ?? "";

// --- Lista blanca de tablas permitidas y su PK ---
$tablasPermitidas = [
  // tabla => campo_id
  "videojuego"   => "id_videojuego",
  "videojuegos"  => "id_videojuego", // por si en el HTML pones "videojuegos"
  // Añade aquí más si quieres permitirlas:
  // "plataforma" => "id_plataforma",
  // "genero"     => "id_genero",
];

try {
  // --- Conexión PDO ---
  $pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
    $user,
    $pass
  );
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // --- Router de acciones ---
  if ($accion === "buscar_por_tabla_id") {

    $tabla = trim((string)($data["tabla"] ?? ""));
    $id    = $data["id"] ?? null;

    // Validaciones
    if ($tabla === "" || !isset($tablasPermitidas[$tabla])) {
      http_response_code(400);
      echo json_encode([
        "ok" => false,
        "error" => "Tabla no permitida",
        "tablas_permitidas" => array_keys($tablasPermitidas)
      ], JSON_PRETTY_PRINT);
      exit;
    }

    if (!is_numeric($id) || (int)$id <= 0) {
      http_response_code(400);
      echo json_encode([
        "ok" => false,
        "error" => "ID inválido"
      ], JSON_PRETTY_PRINT);
      exit;
    }

    $id = (int)$id;
    $campoId = $tablasPermitidas[$tabla];

    // OJO: la tabla y el campoId van “incrustados” porque NO se pueden bindear,
    // pero como vienen de lista blanca es seguro.
    $sql = "SELECT * FROM `$tabla` WHERE `$campoId` = :id LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id" => $id]);

    $registro = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
      "ok" => true,
      "accion" => $accion,
      "tabla" => $tabla,
      "id" => $id,
      "encontrado" => $registro ? true : false,
      "registro" => $registro ? $registro : null
    ], JSON_PRETTY_PRINT);
    exit;
  }

  // Si llega una acción no soportada
  http_response_code(400);
  echo json_encode([
    "ok" => false,
    "error" => "Acción no soportada",
    "accion_recibida" => $accion
  ], JSON_PRETTY_PRINT);
  exit;

} catch (PDOException $e) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => "Error DB",
    "detalle" => $e->getMessage()
  ], JSON_PRETTY_PRINT);
  exit;
}
