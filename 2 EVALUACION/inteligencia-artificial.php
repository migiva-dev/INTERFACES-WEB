<?php

$prompt = "Crea una web personal de portafolio de Jose Vicente Carratala.
Dame solo el codigo HTML y CSS.
Haz un CSS basado en la bandera de españa y franco";

$data = [
  "model"  => "mistral:3b",   // ✅ modelo instalado (según tu ollama list)
  "prompt" => $prompt,
  "stream" => false
];

$ch = curl_init("http://localhost:11434/api/generate");

curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST           => true,
  CURLOPT_HTTPHEADER     => ["Content-Type: application/json"],
  CURLOPT_POSTFIELDS     => json_encode($data, JSON_UNESCAPED_UNICODE),
]);

$response = curl_exec($ch);

if ($response === false) {
  die("Curl error: " . curl_error($ch));
}

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if (!is_array($result)) {
  die("Respuesta no es JSON válido:\n\n" . $response);
}

if ($httpCode < 200 || $httpCode >= 300) {
  die("HTTP $httpCode\n\n" . print_r($result, true));
}

if (isset($result["error"])) {
  die("API error: " . $result["error"]);
}

echo $result["response"] ?? "<pre>No vino 'response'. JSON recibido:\n" . print_r($result, true) . "</pre>";