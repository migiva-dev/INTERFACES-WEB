<?php
header("Content-Type: application/json; charset=utf-8");

$host = "127.0.0.1";
$port = "3306";
$dbname = "videojuegos_asir";
$user = "root";
$pass = "";

function responder($code, $data) {
  http_response_code($code);
  echo json_encode($data, JSON_PRETTY_PRINT);
  exit;
}

function getById(PDO $pdo, int $id) {
  $stmt = $pdo->prepare("
    SELECT id_videojuego, titulo, precio_base
    FROM videojuego
    WHERE id_videojuego = :id
  ");
  $stmt->execute([":id" => $id]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

try {
  $pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
    $user,
    $pass
  );
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(405, ["ok" => false, "error" => "Usa POST"]);
  }

  $accion = $_POST["accion"] ?? "";

  // CREATE
  if ($accion === "add") {
    $titulo = trim($_POST["titulo"] ?? "");
    $precio = $_POST["precio"] ?? null;

    if ($titulo === "" || $precio === null) {
      responder(400, ["ok" => false, "error" => "Faltan campos: titulo, precio"]);
    }

    $stmt = $pdo->prepare("
      INSERT INTO videojuego (titulo, precio_base)
      VALUES (:titulo, :precio)
    ");
    $stmt->execute([
      ":titulo" => $titulo,
      ":precio" => $precio
    ]);

    $id = (int)$pdo->lastInsertId();
    $fila = getById($pdo, $id);

    responder(200, ["ok" => true, "accion" => "add", "fila" => $fila]);
  }

  // UPDATE
  if ($accion === "update") {
    $id     = (int)($_POST["id"] ?? 0);
    $titulo = trim($_POST["titulo"] ?? "");
    $precio = $_POST["precio"] ?? null;

    if ($id <= 0 || $titulo === "" || $precio === null) {
      responder(400, ["ok" => false, "error" => "Faltan campos: id, titulo, precio"]);
    }

    $existe = getById($pdo, $id);
    if (!$existe) {
      responder(404, ["ok" => false, "error" => "No existe ese id"]);
    }

    $stmt = $pdo->prepare("
      UPDATE videojuego
      SET titulo = :titulo,
          precio_base = :precio
      WHERE id_videojuego = :id
    ");
    $stmt->execute([
      ":id"     => $id,
      ":titulo" => $titulo,
      ":precio" => $precio
    ]);

    $fila = getById($pdo, $id);
    responder(200, ["ok" => true, "accion" => "update", "fila" => $fila]);
  }

  // DELETE
  if ($accion === "delete") {
    $id = (int)($_POST["id"] ?? 0);
    if ($id <= 0) {
      responder(400, ["ok" => false, "error" => "Falta campo: id"]);
    }

    $fila = getById($pdo, $id);
    if (!$fila) {
      responder(404, ["ok" => false, "error" => "No existe ese id"]);
    }

    $stmt = $pdo->prepare("DELETE FROM videojuego WHERE id_videojuego = :id");
    $stmt->execute([":id" => $id]);

    responder(200, ["ok" => true, "accion" => "delete", "fila" => $fila]);
  }

  // READ ALL
  if ($accion === "read_all") {
    $stmt = $pdo->query("
      SELECT id_videojuego, titulo, precio_base
      FROM videojuego
      ORDER BY id_videojuego ASC
    ");
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    responder(200, ["ok" => true, "accion" => "read_all", "total" => count($filas), "filas" => $filas]);
  }

  // READ BY ID
  if ($accion === "read_id") {
    $id = (int)($_POST["id"] ?? 0);
    if ($id <= 0) responder(400, ["ok" => false, "error" => "Falta campo: id"]);

    $fila = getById($pdo, $id);
    if (!$fila) responder(404, ["ok" => false, "error" => "No existe ese id"]);

    responder(200, ["ok" => true, "accion" => "read_id", "fila" => $fila]);
  }

  // READ BY TITLE (exacto)
  if ($accion === "read_title") {
    $titulo = trim($_POST["titulo"] ?? "");
    if ($titulo === "") responder(400, ["ok" => false, "error" => "Falta campo: titulo"]);

    $stmt = $pdo->prepare("
      SELECT id_videojuego, titulo, precio_base
      FROM videojuego
      WHERE titulo = :t
      LIMIT 1
    ");
    $stmt->execute([":t" => $titulo]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fila) responder(404, ["ok" => false, "error" => "No existe ese título"]);

    responder(200, ["ok" => true, "accion" => "read_title", "fila" => $fila]);
  }

  responder(400, [
    "ok" => false,
    "error" => "Acción no válida",
    "acciones_validas" => ["add","update","delete","read_all","read_id","read_title"]
  ]);

} catch (PDOException $e) {
  responder(500, ["ok" => false, "error" => "Error: " . $e->getMessage()]);
}