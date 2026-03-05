<?php
declare(strict_types=1);

require_once __DIR__ . '/../Backend/src/db.php';
require_once __DIR__ . '/../Backend/src/auth.php';

require_login();

$serviceId = (int)($_POST['service_id'] ?? 0);
$startIso = $_POST['start_datetime'] ?? '';

if ($serviceId <= 0 || !$startIso) {
  header('Location: index.php');
  exit;
}

$pdo = db();

$st = $pdo->prepare("SELECT * FROM services WHERE id=? AND active=1");
$st->execute([$serviceId]);
$service = $st->fetch();
if (!$service) { header('Location: index.php'); exit; }

$duration = (int)$service['duration_minutes'];

$start = new DateTime($startIso);
$end = (clone $start)->add(new DateInterval('PT' . $duration . 'M'));

$startStr = $start->format('Y-m-d H:i:s');
$endStr = $end->format('Y-m-d H:i:s');

$st = $pdo->prepare("SELECT id FROM appointments
  WHERE status='booked' AND start_datetime < ? AND end_datetime > ? LIMIT 1");
$st->execute([$endStr, $startStr]);
if ($st->fetch()) { header('Location: index.php'); exit; }

$st = $pdo->prepare("SELECT id FROM time_blocks
  WHERE start_datetime < ? AND end_datetime > ? LIMIT 1");
$st->execute([$endStr, $startStr]);
if ($st->fetch()) { header('Location: index.php'); exit; }

$st = $pdo->prepare("INSERT INTO appointments (user_id, service_id, start_datetime, end_datetime, status)
  VALUES (?,?,?,?, 'booked')");
$st->execute([current_user()['id'], $serviceId, $startStr, $endStr]);

header('Location: my_appointments.php');
exit;