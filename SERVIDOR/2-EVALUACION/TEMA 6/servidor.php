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
      id_genero,
      descripcion
    FROM videojuego
    WHERE id_videojuego = :id
  ");
  $stmt->execute([":id" => $id]);
  return $stmt->fetch(PDO::FETCH_ASSOC);
}

function existePorId(PDO $pdo, string $tabla, string $campoId, int $id): bool {
  $stmt = $pdo->prepare("SELECT 1 FROM $tabla WHERE $campoId = :id LIMIT 1");
  $stmt->execute([":id" => $id]);
  return (bool)$stmt->fetchColumn();
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
  // READ ESTUDIOS (para desplegable)
  // =========================
  if ($accion === "read_estudios") {
    $stmt = $pdo->query("SELECT id_estudio, nombre FROM estudio ORDER BY id_estudio ASC");
    $estudios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    responder(200, [
      "ok" => true,
      "accion" => "read_estudios",
      "total" => count($estudios),
      "estudios" => $estudios
    ]);
  }

  // =========================
  // READ GENEROS (para desplegable)
  // =========================
  if ($accion === "read_generos") {
    $stmt = $pdo->query("SELECT id_genero, nombre FROM genero ORDER BY id_genero ASC");
    $generos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    responder(200, [
      "ok" => true,
      "accion" => "read_generos",
      "total" => count($generos),
      "generos" => $generos
    ]);
  }

  // =========================
  // DETALLE: juego + estudio + genero (UNA CONSULTA)
  // Por id o por titulo
  // =========================
  if ($accion === "read_detalle") {
    $id = (int)($_POST["id"] ?? 0);
    $titulo = trim($_POST["titulo"] ?? "");

    if ($id <= 0 && $titulo === "") {
      responder(400, ["ok" => false, "error" => "Pasa id o titulo"]);
    }

    $sql = "
      SELECT
        v.id_videojuego,
        v.titulo,
        e.nombre AS estudio,
        g.nombre AS genero
      FROM videojuego v
      LEFT JOIN estudio e ON e.id_estudio = v.id_estudio
      LEFT JOIN genero  g ON g.id_genero  = v.id_genero
      WHERE (:id > 0 AND v.id_videojuego = :id)
         OR (:id = 0 AND v.titulo = :titulo)
      LIMIT 1
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      ":id" => $id,
      ":titulo" => $titulo
    ]);

    $fila = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$fila) {
      responder(404, ["ok" => false, "error" => "No existe ese juego"]);
    }

    responder(200, [
      "ok" => true,
      "accion" => "read_detalle",
      "detalle" => $fila
    ]);
  }

  // =========================
  // UPDATE RELACIONES (estudio + genero)
  // =========================
  if ($accion === "update_estudio_genero") {
    $id = (int)($_POST["id"] ?? 0);
    $id_estudio = (int)($_POST["id_estudio"] ?? 0);
    $id_genero  = (int)($_POST["id_genero"] ?? 0);

    if ($id <= 0 || $id_estudio <= 0 || $id_genero <= 0) {
      responder(400, ["ok" => false, "error" => "Faltan campos: id, id_estudio, id_genero"]);
    }

    // valida existencia (evita error FK)
    if (!existePorId($pdo, "estudio", "id_estudio", $id_estudio)) {
      responder(400, ["ok" => false, "error" => "id_estudio no existe"]);
    }
    if (!existePorId($pdo, "genero", "id_genero", $id_genero)) {
      responder(400, ["ok" => false, "error" => "id_genero no existe"]);
    }

    // valida juego
    $existeJuego = getById($pdo, $id);
    if (!$existeJuego) {
      responder(404, ["ok" => false, "error" => "No existe ese videojuego"]);
    }

    $stmt = $pdo->prepare("
      UPDATE videojuego
      SET id_estudio = :id_estudio,
          id_genero = :id_genero
      WHERE id_videojuego = :id
    ");
    $stmt->execute([
      ":id" => $id,
      ":id_estudio" => $id_estudio,
      ":id_genero" => $id_genero
    ]);

    $fila = getById($pdo, $id);

    responder(200, [
      "ok" => true,
      "accion" => "update_estudio_genero",
      "fila" => $fila
    ]);
  }

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

    $estudio_raw = trim($_POST["id_estudio"] ?? "");
    $estudio = ($estudio_raw === "") ? null : (int)$estudio_raw;

    $genero_raw = trim($_POST["id_genero"] ?? "");
    $genero = ($genero_raw === "") ? null : (int)$genero_raw;

    $desc   = trim($_POST["descripcion"] ?? "");

    if ($titulo === "" || $precio === null) {
      responder(400, ["ok" => false, "error" => "Faltan campos obligatorios: titulo, precio"]);
    }

    // validar FKs si vienen
    if ($estudio !== null && !existePorId($pdo, "estudio", "id_estudio", $estudio)) {
      responder(400, ["ok" => false, "error" => "id_estudio no existe"]);
    }
    if ($genero !== null && !existePorId($pdo, "genero", "id_genero", $genero)) {
      responder(400, ["ok" => false, "error" => "id_genero no existe"]);
    }

    $stmt = $pdo->prepare("
      INSERT INTO videojuego
        (titulo, precio_base, fecha_lanzamiento, pegi, motor, es_multijugador, id_estudio, id_genero, descripcion)
      VALUES
        (:titulo, :precio, :fecha, :pegi, :motor, :multi, :estudio, :genero, :desc)
    ");
    $stmt->execute([
      ":titulo"  => $titulo,
      ":precio"  => $precio,
      ":fecha"   => ($fecha === "" ? null : $fecha),
      ":pegi"    => ($pegi === "" ? null : $pegi),
      ":motor"   => ($motor === "" ? null : $motor),
      ":multi"   => $multi,
      ":estudio" => $estudio,
      ":genero"  => $genero,
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

    $estudio_raw = trim($_POST["id_estudio"] ?? "");
    $estudio = ($estudio_raw === "") ? null : (int)$estudio_raw;

    $genero_raw = trim($_POST["id_genero"] ?? "");
    $genero = ($genero_raw === "") ? null : (int)$genero_raw;

    $desc   = trim($_POST["descripcion"] ?? "");

    if ($id <= 0) {
      responder(400, ["ok" => false, "error" => "Falta campo obligatorio: id"]);
    }

    $existe = getById($pdo, $id);
    if (!$existe) {
      responder(404, ["ok" => false, "error" => "No existe ese id"]);
    }

    if ($titulo === "" || $precio === null) {
      responder(400, ["ok" => false, "error" => "Faltan campos obligatorios: titulo, precio"]);
    }

    // validar FKs si vienen
    if ($estudio !== null && !existePorId($pdo, "estudio", "id_estudio", $estudio)) {
      responder(400, ["ok" => false, "error" => "id_estudio no existe"]);
    }
    if ($genero !== null && !existePorId($pdo, "genero", "id_genero", $genero)) {
      responder(400, ["ok" => false, "error" => "id_genero no existe"]);
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
        id_genero = :genero,
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
      ":estudio" => $estudio,
      ":genero"  => $genero,
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
        id_genero,
        descripcion
      FROM videojuego
      ORDER BY id_videojuego ASC
    ");
    $filas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    responder(200, [
      "ok" => true,
      "accion" => "read_all",
      "total" => count($filas),
      "filas" => $filas
    ]);
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
  // READ BY TITLE (read_title) exacto
  // =========================
  if ($accion === "read_title") {
    $titulo = trim($_POST["titulo"] ?? "");
    if ($titulo === "") responder(400, ["ok" => false, "error" => "Falta campo: titulo"]);

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
        id_genero,
        descripcion
      FROM videojuego
      WHERE titulo = :t
      LIMIT 1
    ");
    $stmt->execute([":t" => $titulo]);
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$fila) responder(404, ["ok" => false, "error" => "No existe ese título"]);

    responder(200, ["ok" => true, "accion" => "read_title", "fila" => $fila]);
  }

  // Acción no válida
  responder(400, [
    "ok" => false,
    "error" => "Acción no válida",
    "acciones_validas" => [
      "add","update","delete",
      "read_all","read_id","read_title",
      "read_estudios","read_generos",
      "read_detalle","update_estudio_genero"
    ]
  ]);

} catch (PDOException $e) {
  responder(500, ["ok" => false, "error" => "Error: " . $e->getMessage()]);
}