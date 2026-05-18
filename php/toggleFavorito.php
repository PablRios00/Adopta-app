<?php
session_start();
require_once("../includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403); echo "No autorizado"; exit;
}

$idMascota = intval($_POST["idMascota"] ?? 0);
$accion    = trim($_POST["accion"] ?? "");

if (!$idMascota || !in_array($accion, ["añadir", "quitar"])) {
    echo "Datos incorrectos"; exit;
}

try {
    if ($accion === "añadir") {
        $stmt = $conn->prepare("INSERT IGNORE INTO Favoritos (idUsuarioFK, idMascotaFK) VALUES (:uid, :mid)");
    } else {
        $stmt = $conn->prepare("DELETE FROM Favoritos WHERE idUsuarioFK = :uid AND idMascotaFK = :mid");
    }
    $stmt->execute([":uid" => $_SESSION["usuario_id"], ":mid" => $idMascota]);
    echo "success";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>