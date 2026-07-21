<?php
session_start();
require_once("../../includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403); echo "No autorizado"; exit;
}

$emisor    = $_SESSION["usuario_id"];
$receptor  = intval($_POST["idReceptor"]  ?? 0);
$mascota   = intval($_POST["idMascota"]   ?? 0) ?: null;
$contenido = trim($_POST["contenido"]     ?? "");

if (!$receptor || !$contenido) { echo "Datos incompletos"; exit; }
if ($emisor === $receptor)     { echo "No puedes escribirte a ti mismo"; exit; }

try {
    $stmt = $conn->prepare("INSERT INTO Mensaje (idEmisorFK, idReceptorFK, idMascotaFK, contenido)
                            VALUES (:e, :r, :m, :c)");
    $stmt->execute([":e" => $emisor, ":r" => $receptor, ":m" => $mascota, ":c" => $contenido]);
    echo "success";
} catch (PDOException $ex) {
    echo "Error: " . $ex->getMessage();
}
?>