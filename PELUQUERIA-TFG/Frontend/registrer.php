<?php
declare(strict_types=1);

require_once __DIR__ . '/../Backend/src/db.php';
require_once __DIR__ . '/../Backend/src/auth.php';
require_once __DIR__ . '/../Backend/src/helpers.php';

if (current_user()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (strlen($name) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
    $error = 'Revisa los datos (pass mínimo 6).';
  } else {
    try {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $st = db()->prepare("INSERT INTO users (name,email,password_hash,role,phone) VALUES (?,?,?,'client',?)");
      $st->execute([$name, $email, $hash, $phone ?: null]);

      $_SESSION['user'] = ['id'=>(int)db()->lastInsertId(),'name'=>$name,'email'=>$email,'role'=>'client'];
      header('Location: index.php');
      exit;
    } catch (PDOException $e) {
      $error = str_contains($e->getMessage(), 'Duplicate') ? 'Ese email ya existe' : 'Error al registrar';
    }
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registro</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <div class="brand">✂️ Barbería</div>
    <div style="display:flex; gap:10px;">
      <a href="index.php">Volver</a>
      <a href="login.php">Entrar</a>
    </div>
  </div>

  <div class="hr"></div>

  <div class="card" style="max-width:520px; margin:0 auto;">
    <h2>Registro</h2>
    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" class="grid" style="gap:12px; margin-top:12px;">
      <div>
        <label>Nombre</label>
        <input name="name" required>
      </div>
      <div>
        <label>Teléfono (opcional)</label>
        <input name="phone">
      </div>
      <div>
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div>
        <label>Contraseña</label>
        <input type="password" name="password" required>
      </div>
      <button class="btn btn-accent">Crear cuenta</button>
    </form>
  </div>
</div>
</body>
</html>