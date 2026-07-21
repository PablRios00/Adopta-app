<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) {
    echo json_encode(["logueado" => false, "usuario" => null, "id" => null]);
    exit;
}

echo json_encode([
    "logueado" => true,
    "usuario"  => $_SESSION["usuario_nombre"],
    "id"       => $_SESSION["usuario_id"]
]);
?>