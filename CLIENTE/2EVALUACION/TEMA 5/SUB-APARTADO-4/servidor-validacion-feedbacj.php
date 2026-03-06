<?php
header("Content-Type: application/json; charset=utf-8");

// CORS (por si abres cliente.html fuera del servidor)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") { http_response_code(204); exit; }

// ==== CONFIG BD ====
$host = "127.0.0.1";
$port = "3306";
$dbname = "videojuegos_asir";
$user = "root";
$pass = "";

function responder(int $code, array $data) {
  http_response_code($code);
  echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
  exit;
}

function getById(PDO $pdo, int $id) {
  $stmt = $pdo->prepare("
    SELECT
      id_videojuego,
      titulo,
      fecha_lanzamiento,
      pegi,
      precio_base,
      motor,
      es_multijugador,
      descripcion
    FROM videojuego
    WHERE id_videojuego = :id
  ");
  $stmt->execute([":id"=>$id]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

try {
  $pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
    $user, $pass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
  );

  if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(405, ["ok"=>false, "error"=>"Usa POST"]);
  }

  $accion = $_POST["accion"] ?? "";

  // =========================
  // READ ALL
  // =========================
  if ($accion === "read_all") {
    $stmt = $pdo->query("
      SELECT
        id_videojuego,
        titulo,
        fecha_lanzamiento,
        pegi,
        precio_base,
        motor,
        es_multijugador,
        descripcion
      FROM videojuego
      ORDER BY id_videojuego ASC
    ");
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    responder(200, ["ok"=>true, "accion"=>$accion, "total"=>count($filas), "filas"=>$filas]);
  }

  // =========================
  // READ BY ID
  // =========================
  if ($accion === "read_id") {
    $id = (int)($_POST["id"] ?? 0);
    if ($id <= 0) responder(400, ["ok"=>false, "error"=>"Falta campo: id"]);

    $fila = getById($pdo, $id);
    if (!$fila) responder(404, ["ok"=>false, "error"=>"No existe ese id"]);

    responder(200, ["ok"=>true, "accion"=>$accion, "fila"=>$fila]);
  }

  // =========================
  // READ BY TITLE (exacto)
  // =========================
  if ($accion === "read_title") {
    $titulo = trim($_POST["titulo"] ?? "");
    if ($titulo === "") responder(400, ["ok"=>false, "error"=>"Falta campo: titulo"]);

    $stmt = $pdo->prepare("
      SELECT
        id_videojuego,
        titulo,
        fecha_lanzamiento,
        pegi,
        precio_base,
        motor,
        es_multijugador,
        descripcion
      FROM videojuego
      WHERE titulo = :t
      LIMIT 1
    ");
    $stmt->execute([":t"=>$titulo]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fila) responder(404, ["ok"=>false, "error"=>"No existe ese título"]);
    responder(200, ["ok"=>true, "accion"=>$accion, "fila"=>$fila]);
  }

  // =========================
  // CREATE (ADD) -> devuelve fila añadida
  // =========================
  if ($accion === "add") {
    $titulo = trim($_POST["titulo"] ?? "");
    $precio = $_POST["precio"] ?? null;

    $fecha = $_POST["fecha_lanzamiento"] ?? null;
    $pegi  = $_POST["pegi"] ?? null;
    $motor = trim($_POST["motor"] ?? "");
    $multi = isset($_POST["es_multijugador"]) ? (int)$_POST["es_multijugador"] : 0;
    $desc  = trim($_POST["descripcion"] ?? "");

    if ($titulo === "" || $precio === null) {
      responder(400, ["ok"=>false, "error"=>"Obligatorio: titulo, precio"]);
    }

    $stmt = $pdo->prepare("
      INSERT INTO videojuego
        (titulo, fecha_lanzamiento, pegi, precio_base, motor, es_multijugador, descripcion)
      VALUES
        (:titulo, :fecha, :pegi, :precio, :motor, :multi, :desc)
    ");
    $stmt->execute([
      ":titulo"=>$titulo,
      ":fecha"=>($fecha===""?null:$fecha),
      ":pegi"=>($pegi===""?null:$pegi),
      ":precio"=>$precio,
      ":motor"=>($motor===""?null:$motor),
      ":multi"=>$multi,
      ":desc"=>($desc===""?null:$desc),
    ]);

    $id = (int)$pdo->lastInsertId();
    $fila = getById($pdo, $id);

    responder(200, ["ok"=>true, "accion"=>$accion, "fila"=>$fila]);
  }

  // =========================
  // UPDATE -> devuelve fila actualizada
  // =========================
  if ($accion === "update") {
    $id = (int)($_POST["id"] ?? 0);
    if ($id <= 0) responder(400, ["ok"=>false, "error"=>"Falta campo: id"]);
    if (!getById($pdo, $id)) responder(404, ["ok"=>false, "error"=>"No existe ese id"]);

    $titulo = trim($_POST["titulo"] ?? "");
    $precio = $_POST["precio"] ?? null;

    $fecha = $_POST["fecha_lanzamiento"] ?? null;
    $pegi  = $_POST["pegi"] ?? null;
    $motor = trim($_POST["motor"] ?? "");
    $multi = isset($_POST["es_multijugador"]) ? (int)$_POST["es_multijugador"] : 0;
    $desc  = trim($_POST["descripcion"] ?? "");

    if ($titulo === "" || $precio === null) {
      responder(400, ["ok"=>false, "error"=>"Obligatorio: titulo, precio"]);
    }

    $stmt = $pdo->prepare("
      UPDATE videojuego
      SET titulo=:titulo,
          fecha_lanzamiento=:fecha,
          pegi=:pegi,
          precio_base=:precio,
          motor=:motor,
          es_multijugador=:multi,
          descripcion=:desc
      WHERE id_videojuego=:id
    ");
    $stmt->execute([
      ":id"=>$id,
      ":titulo"=>$titulo,
      ":fecha"=>($fecha===""?null:$fecha),
      ":pegi"=>($pegi===""?null:$pegi),
      ":precio"=>$precio,
      ":motor"=>($motor===""?null:$motor),
      ":multi"=>$multi,
      ":desc"=>($desc===""?null:$desc),
    ]);

    $fila = getById($pdo, $id);
    responder(200, ["ok"=>true, "accion"=>$accion, "fila"=>$fila]);
  }

  // =========================
  // DELETE -> devuelve fila borrada
  // =========================
  if ($accion === "delete") {
    $id = (int)($_POST["id"] ?? 0);
    if ($id <= 0) responder(400, ["ok"=>false, "error"=>"Falta campo: id"]);

    $fila = getById($pdo, $id);
    if (!$fila) responder(404, ["ok"=>false, "error"=>"No existe ese id"]);

    $stmt = $pdo->prepare("DELETE FROM videojuego WHERE id_videojuego = :id");
    $stmt->execute([":id"=>$id]);

    responder(200, ["ok"=>true, "accion"=>$accion, "fila"=>$fila]);
  }

  responder(400, ["ok"=>false, "error"=>"Acción no válida"]);

} catch (PDOException $e) {
  responder(500, ["ok"=>false, "error"=>"Error: ".$e->getMessage()]);
}