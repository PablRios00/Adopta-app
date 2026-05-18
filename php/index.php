<?php
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_nombre"])) {
  http_response_code(403);
  echo json_encode(["error" => "No autorizado"]);
  exit;
}

echo json_encode([
    "usuario" => $_SESSION["usuario_nombre"],
    "id"      => $_SESSION["usuario_id"]
]);