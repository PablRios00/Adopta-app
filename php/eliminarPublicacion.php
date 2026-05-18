<?php
session_start();
require_once("../includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403);
    echo "No autorizado";
    exit;
}

$id = intval($_POST["idMascota"] ?? 0);

if (!$id) {
    echo "ID no válido.";
    exit;
}

// Verificar que la publicación pertenece al usuario en sesión
$check = $conn->prepare("SELECT idMascota FROM Mascota WHERE idMascota = :id AND idUsuarioFK1 = :uid");
$check->execute([":id" => $id, ":uid" => $_SESSION["usuario_id"]]);

if (!$check->fetch()) {
    echo "No tienes permiso para eliminar esta publicación.";
    exit;
}

try {
    // Primero eliminar adopciones relacionadas (FK)
    $del1 = $conn->prepare("DELETE FROM Adopcion WHERE idMascotaFK3 = :id");
    $del1->execute([":id" => $id]);

    // Luego eliminar la mascota
    $del2 = $conn->prepare("DELETE FROM Mascota WHERE idMascota = :id");
    $del2->execute([":id" => $id]);

    echo "success";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>