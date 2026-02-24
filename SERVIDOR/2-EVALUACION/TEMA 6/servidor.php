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
    SELECT
      id_videojuego,
      titulo,
      precio_base,
      fecha_lanzamiento,
      pegi,
      motor,
      es_multijugador,
      id_estudio,
      descripcion
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

  // =========================
  // CREATE (add)
  // =========================
  if ($accion === "add") {
    $titulo = trim($_POST["titulo"] ?? "");
    $precio = $_POST["precio"] ?? null;

    $fecha  = $_POST["fecha_lanzamiento"] ?? null; // YYYY-MM-DD
    $pegi   = $_POST["pegi"] ?? null;
    $motor  = trim($_POST["motor"] ?? "");
    $multi  = isset($_POST["es_multijugador"]) ? (int)$_POST["es_multijugador"] : 0;
    $estudio = $_POST["id_estudio"] ?? null;
    $desc   = trim($_POST["descripcion"] ?? "");

    // Validación mínima (ajusta si tu profe lo exige)
    if ($titulo === "" || $precio === null) {
      responder(400, ["ok" => false, "error" => "Faltan campos obligatorios: titulo, precio"]);
    }

    $stmt = $pdo->prepare("
      INSERT INTO videojuego
        (titulo, precio_base, fecha_lanzamiento, pegi, motor, es_multijugador, id_estudio, descripcion)
      VALUES
        (:titulo, :precio, :fecha, :pegi, :motor, :multi, :estudio, :desc)
    ");
    $stmt->execute([
      ":titulo"  => $titulo,
      ":precio"  => $precio,
      ":fecha"   => ($fecha === "" ? null : $fecha),
      ":pegi"    => ($pegi === "" ? null : $pegi),
      ":motor"   => ($motor === "" ? null : $motor),
      ":multi"   => $multi,
      ":estudio" => ($estudio === "" ? null : $estudio),
      ":desc"    => ($desc === "" ? null : $desc),
    ]);

    $id = (int)$pdo->lastInsertId();
    $fila = getById($pdo, $id);

    responder(200, ["ok" => true, "accion" => "add", "fila" => $fila]);
  }

  // =========================
  // UPDATE (update)
  // =========================
  if ($accion === "update") {
    $id     = (int)($_POST["id"] ?? 0);
    $titulo = trim($_POST["titulo"] ?? "");
    $precio = $_POST["precio"] ?? null;

    $fecha  = $_POST["fecha_lanzamiento"] ?? null;
    $pegi   = $_POST["pegi"] ?? null;
    $motor  = trim($_POST["motor"] ?? "");
    $multi  = isset($_POST["es_multijugador"]) ? (int)$_POST["es_multijugador"] : 0;
    $estudio = $_POST["id_estudio"] ?? null;
    $desc   = trim($_POST["descripcion"] ?? "");

    if ($id <= 0) {
      responder(400, ["ok" => false, "error" => "Falta campo obligatorio: id"]);
    }

    $existe = getById($pdo, $id);
    if (!$existe) {
      responder(404, ["ok" => false, "error" => "No existe ese id"]);
    }

    // Si quieres obligatorios en update, deja esta validación:
    if ($titulo === "" || $precio === null) {
      responder(400, ["ok" => false, "error" => "Faltan campos obligatorios: titulo, precio"]);
    }

    $stmt = $pdo->prepare("
      UPDATE videojuego
      SET
        titulo = :titulo,
        precio_base = :precio,
        fecha_lanzamiento = :fecha,
        pegi = :pegi,
        motor = :motor,
        es_multijugador = :multi,
        id_estudio = :estudio,
        descripcion = :desc
      WHERE id_videojuego = :id
    ");
    $stmt->execute([
      ":id"      => $id,
      ":titulo"  => $titulo,
      ":precio"  => $precio,
      ":fecha"   => ($fecha === "" ? null : $fecha),
      ":pegi"    => ($pegi === "" ? null : $pegi),
      ":motor"   => ($motor === "" ? null : $motor),
      ":multi"   => $multi,
      ":estudio" => ($estudio === "" ? null : $estudio),
      ":desc"    => ($desc === "" ? null : $desc),
    ]);

    $fila = getById($pdo, $id);
    responder(200, ["ok" => true, "accion" => "update", "fila" => $fila]);
  }

  // =========================
  // DELETE (delete)
  // =========================
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

  // =========================
  // READ ALL (read_all)
  // =========================
  if ($accion === "read_all") {
    $stmt = $pdo->query("
      SELECT
        id_videojuego,
        titulo,
        precio_base,
        fecha_lanzamiento,
        pegi,
        motor,
        es_multijugador,
        id_estudio,
        descripcion
      FROM videojuego
      ORDER BY id_videojuego ASC
    ");
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    responder(200, ["ok" => true, "accion" => "read_all", "total" => count($filas), "filas" => $filas]);
  }

  // =========================
  // READ BY ID (read_id)
  // =========================
  if ($accion === "read_id") {
    $id = (int)($_POST["id"] ?? 0);
    if ($id <= 0) responder(400, ["ok" => false, "error" => "Falta campo: id"]);

    $fila = getById($pdo, $id);
    if (!$fila) responder(404, ["ok" => false, "error" => "No existe ese id"]);

    responder(200, ["ok" => true, "accion" => "read_id", "fila" => $fila]);
  }

  // =========================
  // READ BY TITLE (read_title) (exacto)
  // =========================
  if ($accion === "read_title") {
    $titulo = trim($_POST["titulo"] ?? "");
    if ($titulo === "") responder(400, ["ok" => false, "error" => "Falta campo: titulo"]);

    $stmt = $pdo->prepare("
      SELECT
      id_videojuego, titulo, fecha_lanzamiento, pegi, precio_base, motor, es_multijugador, id_estudio, descripcion
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