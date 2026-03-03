<?php

    header('Content-Type: application/json; charset=utf-8');

    $host = "127.0.0.1";
    $port = "3306";
    $dbname = "videojuegos_asir";
    $user = "root";
    $pass = "";

    $mensaje = "";
    $desarrolladores = []

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
?>