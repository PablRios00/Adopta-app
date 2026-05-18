<?php
session_start();
require_once("../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION["usuario_id"])) { http_response_code(403); exit; }
$uid = $_SESSION["usuario_id"];

// Desactivar only_full_group_by para esta sesión
$conn->exec("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

$stmt = $conn->prepare("
    SELECT
        u.idUsuario      AS otroId,
        u.nombreUsuario  AS otroUsuario,
        (u.fotoPerfil IS NOT NULL AND LENGTH(u.fotoPerfil) > 0) AS tieneFoto,
        msg.contenido    AS ultimoMensaje,
        msg.fechaMensaje AS fecha,
        msg.idMascotaFK  AS idMascota,
        ma.nombreMascota AS nombreMascota,
        SUM(CASE WHEN msg2.leido=0 AND msg2.idReceptorFK=:uid2 THEN 1 ELSE 0 END) AS noLeidos
    FROM (
        SELECT DISTINCT
            CASE WHEN idEmisorFK=:uid3 THEN idReceptorFK ELSE idEmisorFK END AS contactoId,
            idMascotaFK
        FROM Mensaje
        WHERE idEmisorFK=:uid4 OR idReceptorFK=:uid5
    ) c
    JOIN Usuario u ON u.idUsuario = c.contactoId
    LEFT JOIN Mascota ma ON ma.idMascota = c.idMascotaFK
    JOIN Mensaje msg ON msg.idMensaje = (
        SELECT idMensaje FROM Mensaje
        WHERE ((idEmisorFK=:uid6 AND idReceptorFK=u.idUsuario)
            OR (idEmisorFK=u.idUsuario AND idReceptorFK=:uid7))
          AND (
            (c.idMascotaFK IS NULL     AND idMascotaFK IS NULL) OR
            (c.idMascotaFK IS NOT NULL AND idMascotaFK = c.idMascotaFK)
          )
        ORDER BY fechaMensaje DESC LIMIT 1
    )
    LEFT JOIN Mensaje msg2 ON
        msg2.idEmisorFK = u.idUsuario
        AND msg2.idReceptorFK = :uid8
        AND (
            (c.idMascotaFK IS NULL     AND msg2.idMascotaFK IS NULL) OR
            (c.idMascotaFK IS NOT NULL AND msg2.idMascotaFK = c.idMascotaFK)
        )
    GROUP BY u.idUsuario, u.nombreUsuario, u.fotoPerfil, c.idMascotaFK,
             ma.nombreMascota, msg.contenido, msg.fechaMensaje, msg.idMascotaFK
    ORDER BY msg.fechaMensaje DESC
");

$stmt->execute([
    ":uid2"=>$uid, ":uid3"=>$uid, ":uid4"=>$uid,
    ":uid5"=>$uid, ":uid6"=>$uid, ":uid7"=>$uid, ":uid8"=>$uid
]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>