<?php
session_start();
require_once("../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) { http_response_code(403); exit; }

$uid     = $_SESSION["usuario_id"];
$con     = intval($_GET["con"] ?? 0);
$tieneMascota = isset($_GET["mascota"]) && $_GET["mascota"] !== "";
$mascota = $tieneMascota ? intval($_GET["mascota"]) : null;

if (!$con) { echo json_encode([]); exit; }

// Marcar como leídos
if ($tieneMascota) {
    $upd = $conn->prepare("UPDATE Mensaje SET leido=1
                           WHERE idEmisorFK=:con AND idReceptorFK=:uid
                           AND idMascotaFK=:mid AND leido=0");
    $upd->execute([":con"=>$con, ":uid"=>$uid, ":mid"=>$mascota]);
} else {
    $upd = $conn->prepare("UPDATE Mensaje SET leido=1
                           WHERE idEmisorFK=:con AND idReceptorFK=:uid
                           AND idMascotaFK IS NULL AND leido=0");
    $upd->execute([":con"=>$con, ":uid"=>$uid]);
}

// Obtener mensajes
if ($tieneMascota) {
    $stmt = $conn->prepare("
        SELECT m.idMensaje, m.contenido, m.fechaMensaje, m.leido,
               m.idEmisorFK, u.nombreUsuario AS nombreEmisor
        FROM Mensaje m
        JOIN Usuario u ON u.idUsuario = m.idEmisorFK
        WHERE ((m.idEmisorFK=:uid  AND m.idReceptorFK=:con)
            OR (m.idEmisorFK=:con2 AND m.idReceptorFK=:uid2))
          AND m.idMascotaFK = :mid
        ORDER BY m.fechaMensaje ASC
    ");
    $stmt->execute([":uid"=>$uid,":con"=>$con,":con2"=>$con,":uid2"=>$uid,":mid"=>$mascota]);
} else {
    $stmt = $conn->prepare("
        SELECT m.idMensaje, m.contenido, m.fechaMensaje, m.leido,
               m.idEmisorFK, u.nombreUsuario AS nombreEmisor
        FROM Mensaje m
        JOIN Usuario u ON u.idUsuario = m.idEmisorFK
        WHERE ((m.idEmisorFK=:uid  AND m.idReceptorFK=:con)
            OR (m.idEmisorFK=:con2 AND m.idReceptorFK=:uid2))
        ORDER BY m.fechaMensaje ASC
    ");
    $stmt->execute([":uid"=>$uid,":con"=>$con,":con2"=>$con,":uid2"=>$uid]);
}

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>