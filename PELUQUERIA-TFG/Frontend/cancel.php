<?php
declare(strict_types=1);

require_once __DIR__ . '/../Backend/src/db.php';
require_once __DIR__ . '/../Backend/src/auth.php';

require_login();

$id = (int)($_POST['id'] ?? 0);
if ($id > 0) {
  $st = db()->prepare("UPDATE appointments SET status='cancelled' WHERE id=? AND user_id=?");
  $st->execute([$id, current_user()['id']]);
}

header('Location: my_appointments.php');
exit;