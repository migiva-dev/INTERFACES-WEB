<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');



$host = "127.0.0.1";
$port = "3306";
$dbname = "videojuegos_asir";
$user = "root";
$pass = "";


function json_out(int $status, array $payload): void {
  http_response_code($status);
  echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  exit;
}

function read_input(): array {
  $ct = $_SERVER['CONTENT_TYPE'] ?? '';
  if (stripos($ct, 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '[]', true);
    return is_array($data) ? $data : [];
  }
  return $_POST ?? [];
}

function to_null_if_empty($v) {
  if ($v === null) return null;
  if (!is_string($v)) return $v;
  $t = trim($v);
  return ($t === '') ? null : $t;
}

function today_ymd(): string {
  return (new DateTimeImmutable('today'))->format('Y-m-d');
}

function is_future_date(?string $ymd): bool {
  if (!$ymd) return false;
  return $ymd > today_ymd();
}

function clamp_len(?string $s, int $max): ?string {
  if ($s === null) return null;
  if (mb_strlen($s) > $max) return mb_substr($s, 0, $max);
  return $s;
}

function sanitize_nombre_like(?string $s): ?string {
  $s = to_null_if_empty($s);
  if ($s === null) return null;
  $s = preg_replace("/[^A-Za-zÁÉÍÓÚÜÑáéíóúüñ\s'\-]/u", "", $s);
  return trim((string)$s);
}

function sanitize_email(?string $s): ?string {
  $s = to_null_if_empty($s);
  if ($s === null) return null;
  $s = preg_replace('/\s+/', '', $s);
  return strtolower((string)$s);
}

// ---------- PDO ----------
try {
  $pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
    $user,
    $pass,
    [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
  );
} catch (Throwable $e) {
  json_out(500, ['ok' => false, 'message' => 'Error de conexión a la base de datos.', 'detail' => $e->getMessage()]);
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$action = $_GET['action'] ?? '';

// ---------- GET estudios ----------
if ($method === 'GET' && $action === 'estudios') {
  try {
    $stmt = $pdo->query("SELECT id_estudio AS id, nombre FROM estudio ORDER BY nombre ASC");
    $rows = $stmt->fetchAll();
    json_out(200, ['ok' => true, 'data' => $rows]);
  } catch (Throwable $e) {
    json_out(500, ['ok' => false, 'message' => 'Error obteniendo estudios.', 'detail' => $e->getMessage()]);
  }
}

// ---------- POST guardar desarrollador ----------
if ($method !== 'POST') {
  json_out(405, ['ok' => false, 'message' => 'Método no permitido.']);
}

$in = read_input();

// Longitudes según tu tabla desarrollador
$MAX = [
  'nombre' => 80,
  'apellido' => 120,
  'email' => 150,
  'ciudad' => 80,
  'pais' => 80,
];

// Normaliza + limpia
$nombre = clamp_len(sanitize_nombre_like($in['nombre'] ?? null), $MAX['nombre']);
$apellido = clamp_len(sanitize_nombre_like($in['apellido'] ?? null), $MAX['apellido']);
$email = clamp_len(sanitize_email($in['email'] ?? null), $MAX['email']);
$ciudad = clamp_len(sanitize_nombre_like($in['ciudad'] ?? null), $MAX['ciudad']);
$pais = clamp_len(sanitize_nombre_like($in['pais'] ?? null), $MAX['pais']);

$fecha_nacimiento = to_null_if_empty($in['fecha_nacimiento'] ?? null);
$fecha_alta = to_null_if_empty($in['fecha_alta'] ?? null);

// activo puede venir true/false o string
$activo = $in['activo'] ?? false;
if (is_string($activo)) {
  $al = strtolower(trim($activo));
  $activo = ($al === 'true' || $al === '1' || $al === 'on' || $al === 'yes');
} else {
  $activo = (bool)$activo;
}

// id_estudio (puede ser null/"")
$id_estudio_raw = $in['id_estudio'] ?? null;
$id_estudio_raw = to_null_if_empty(is_string($id_estudio_raw) ? $id_estudio_raw : (string)$id_estudio_raw);
$id_estudio = null;
if ($id_estudio_raw !== null && $id_estudio_raw !== '') {
  $id_estudio = ctype_digit((string)$id_estudio_raw) ? (int)$id_estudio_raw : null;
}

// ---------- Validación (lo que te piden) ----------
$errors = [];

// nombre obligatorio min 2
if ($nombre === null || mb_strlen($nombre) < 2) {
  $errors['nombre'] = 'El nombre es obligatorio y debe tener al menos 2 caracteres.';
}

// apellido obligatorio min 2
if ($apellido === null || mb_strlen($apellido) < 2) {
  $errors['apellido'] = 'El apellido es obligatorio y debe tener al menos 2 caracteres.';
}

// email opcional, si está => contiene @
if ($email !== null && strpos($email, '@') === false) {
  $errors['email'] = 'El email debe contener @.';
}

// ciudad opcional, si está => min 2
if ($ciudad !== null && mb_strlen($ciudad) < 2) {
  $errors['ciudad'] = 'La ciudad debe tener al menos 2 caracteres si está rellena.';
}

// pais opcional, si está => min 2
if ($pais !== null && mb_strlen($pais) < 2) {
  $errors['pais'] = 'El país debe tener al menos 2 caracteres si está relleno.';
}

// fecha_nacimiento opcional, si está => no futura
if ($fecha_nacimiento !== null && is_future_date($fecha_nacimiento)) {
  $errors['fecha_nacimiento'] = 'La fecha de nacimiento no puede ser futura.';
}

if ($activo) {
  // activo marcado:
  // fecha_alta obligatoria y no futura
  if ($fecha_alta === null) {
    $errors['fecha_alta'] = 'La fecha de alta es obligatoria si Activo está marcado.';
  } elseif (is_future_date($fecha_alta)) {
    $errors['fecha_alta'] = 'La fecha de alta no puede ser futura.';
  }

  // id_estudio obligatorio (no puede ser Sin estudio => null)
  if ($id_estudio === null) {
    $errors['id_estudio'] = 'Debes seleccionar un estudio (no puede ser “Sin estudio”).';
  } else {
    // comprobar que existe ese estudio
    $chk = $pdo->prepare("SELECT 1 FROM estudio WHERE id_estudio = :id LIMIT 1");
    $chk->execute([':id' => $id_estudio]);
    if (!$chk->fetchColumn()) {
      $errors['id_estudio'] = 'El estudio seleccionado no existe.';
    }
  }
} else {
  // NO activo:
  // id_estudio debe ser null
  $id_estudio = null;

  // fecha_alta no puede ser futura si está rellena
  if ($fecha_alta !== null && is_future_date($fecha_alta)) {
    $errors['fecha_alta'] = 'La fecha de alta no puede ser futura.';
  }

  // 👇 Por tu BD (fecha_alta NOT NULL), si viene vacía guardamos hoy
  if ($fecha_alta === null) {
    $fecha_alta = today_ymd();
  }
}

if (!empty($errors)) {
  json_out(422, ['ok' => false, 'errors' => $errors]);
}

// ---------- INSERT en desarrollador ----------
try {
  $sql = "
    INSERT INTO desarrollador
      (nombre, apellido, email, ciudad, pais, fecha_nacimiento, fecha_alta, activo, id_estudio)
    VALUES
      (:nombre, :apellido, :email, :ciudad, :pais, :fecha_nacimiento, :fecha_alta, :activo, :id_estudio)
  ";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    ':nombre' => $nombre,
    ':apellido' => $apellido,
    ':email' => $email,
    ':ciudad' => $ciudad,
    ':pais' => $pais,
    ':fecha_nacimiento' => $fecha_nacimiento,
    ':fecha_alta' => $fecha_alta,
    ':activo' => $activo ? 1 : 0,
    ':id_estudio' => $id_estudio
  ]);

  $newId = (int)$pdo->lastInsertId();

  json_out(200, [
    'ok' => true,
    'id_desarrollador' => $newId,
    'saved' => [
      'nombre' => $nombre,
      'apellido' => $apellido,
      'email' => $email,
      'ciudad' => $ciudad,
      'pais' => $pais,
      'fecha_nacimiento' => $fecha_nacimiento,
      'fecha_alta' => $fecha_alta,
      'activo' => $activo,
      'id_estudio' => $id_estudio,
    ]
  ]);
} catch (Throwable $e) {
  json_out(500, ['ok' => false, 'message' => 'Error al guardar en la base de datos.', 'detail' => $e->getMessage()]);
}