<?php
declare(strict_types=1);
require_once __DIR__ . '/../Backend/src/auth.php';

$_SESSION = [];
session_destroy();
header('Location: index.php');
exit;