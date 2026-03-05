<?php
declare(strict_types=1);

require_once __DIR__ . '/../Backend/src/db.php';
require_once __DIR__ . '/../Backend/src/auth.php';
require_once __DIR__ . '/../Backend/src/helpers.php';

if (current_user()) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  $st = db()->prepare("SELECT id,name,email,role,password_hash FROM users WHERE email=?");
  $st->execute([$email]);
  $u = $st->fetch();

  if (!$u || !password_verify($password, $u['password_hash'])) {
    $error = 'Credenciales incorrectas';
  } else {
    $_SESSION['user'] = ['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']];
    header('Location: index.php');
    exit;
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Entrar</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <div class="brand">✂️ Barbería</div>
    <div style="display:flex; gap:10px;">
      <a href="index.php">Volver</a>
      <a href="register.php">Registro</a>
    </div>
  </div>

  <div class="hr"></div>

  <div class="card" style="max-width:520px; margin:0 auto;">
    <h2>Entrar</h2>
    <?php if ($error): ?><div class="error"><?= e($error) ?></div><?php endif; ?>

    <form method="POST" class="grid" style="gap:12px; margin-top:12px;">
      <div>
        <label>Email</label>
        <input type="email" name="email" required>
      </div>
      <div>
        <label>Contraseña</label>
        <input type="password" name="password" required>
      </div>
      <button class="btn btn-accent">Entrar</button>
    </form>
  </div>
</div>
</body>
</html>