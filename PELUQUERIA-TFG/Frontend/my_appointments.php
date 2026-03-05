<?php
declare(strict_types=1);

require_once __DIR__ . '/../Backend/src/db.php';
require_once __DIR__ . '/../Backend/src/auth.php';
require_once __DIR__ . '/../Backend/src/helpers.php';

require_login();

$user = current_user();
$pdo = db();

$st = $pdo->prepare("SELECT a.*, s.name AS service_name
  FROM appointments a
  JOIN services s ON s.id=a.service_id
  WHERE a.user_id=?
  ORDER BY a.start_datetime DESC");
$st->execute([$user['id']]);
$items = $st->fetchAll();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Mis citas</title>
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="container">
  <div class="nav">
    <div class="brand">✂️ Barbería</div>
    <div style="display:flex; gap:10px; align-items:center;">
      <a href="index.php">Servicios</a>
      <a class="badge" href="logout.php">Salir (<?= e($user['name']) ?>)</a>
    </div>
  </div>

  <div class="hr"></div>

  <div class="card">
    <h2>Mis citas</h2>

    <?php if (!$items): ?>
      <small>No tienes citas.</small>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>Fecha</th><th>Servicio</th><th>Estado</th><th></th></tr>
        </thead>
        <tbody>
          <?php foreach ($items as $a): ?>
            <tr>
              <td><?= e(date('d/m/Y H:i', strtotime($a['start_datetime']))) ?></td>
              <td><?= e($a['service_name']) ?></td>
              <td><span class="badge"><?= e($a['status']) ?></span></td>
              <td>
                <?php if ($a['status'] === 'booked'): ?>
                  <form method="POST" action="cancel.php" style="margin:0;">
                    <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                    <button class="btn btn-danger" onclick="return confirm('¿Cancelar la cita?')">Cancelar</button>
                  </form>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>
</body>
</html>