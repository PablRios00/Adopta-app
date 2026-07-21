<?php
session_start();
require_once("../../includes/config.php");
require_once("../../includes/conexion.php");
require_once("../../includes/mailer.php");

if (!isset($_SESSION["usuario_id"])) {
    http_response_code(403); echo "No autorizado"; exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $idUsuario   = $_SESSION["usuario_id"];
    $nombre      = trim($_POST["nombreMascota"]        ?? "");
    $especie     = trim($_POST["especieMascota"]        ?? "");
    $sexo        = trim($_POST["sexoMascota"]           ?? "");
    $edad        = trim($_POST["edadMascota"]           ?? "");
    $raza        = trim($_POST["razaMascota"]           ?? "");
    $descripcion = trim($_POST["descripcionMascota"]    ?? "");
    $estado      = trim($_POST["estadoAdopcionMascota"] ?? "");
    $lugar       = trim($_POST["lugarMascota"]          ?? "");
    $municipio   = trim($_POST["municipioMascota"]      ?? "");
    $provincia   = trim($_POST["provinciaMascota"]      ?? "");
    $comunidad   = trim($_POST["comunidadMascota"]      ?? "");
    $latitud     = isset($_POST["latitudMascota"])  && $_POST["latitudMascota"]  !== "" ? floatval($_POST["latitudMascota"])  : null;
    $longitud    = isset($_POST["longitudMascota"]) && $_POST["longitudMascota"] !== "" ? floatval($_POST["longitudMascota"]) : null;

    if (empty($nombre) || empty($especie) || empty($sexo) ||
        empty($edad)   || empty($raza)   || empty($descripcion) ||
        empty($estado) || empty($lugar)) {
        echo "Por favor completa todos los campos."; exit;
    }

    // ── Validar y leer imágenes con verificación MIME y tamaño ──
    function validarImagen($key) {
        if (empty($_FILES[$key]["tmp_name"])) return null;

        $tmpPath = $_FILES[$key]["tmp_name"];
        $size    = $_FILES[$key]["size"];

        // Verificar tamaño
        if ($size > IMG_MAX_SIZE_BYTES) {
            echo "La imagen '{$key}' supera el tamaño máximo permitido de " . IMG_MAX_SIZE_MB . "MB.";
            exit;
        }

        // Verificar tipo MIME real (no el que dice el navegador)
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mimeReal = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!in_array($mimeReal, IMG_TIPOS_PERMITIDOS)) {
            echo "El archivo '{$key}' no es una imagen válida. Solo se permiten JPG, PNG, GIF y WebP.";
            exit;
        }

        // Verificar que es una imagen real con getimagesize
        $info = @getimagesize($tmpPath);
        if (!$info) {
            echo "El archivo '{$key}' no es una imagen válida.";
            exit;
        }

        return file_get_contents($tmpPath);
    }

    $foto1 = validarImagen("foto1");
    $foto2 = validarImagen("foto2");
    $foto3 = validarImagen("foto3");

    if (!$foto1) { echo "Debes subir al menos una imagen."; exit; }

    $fechaPublicacion = date("Y-m-d");

    try {
        $stmt = $conn->prepare("INSERT INTO Mascota
            (nombreMascota, especieMascota, sexoMascota, edadMascota, razaMascota,
             descripcionMascota, lugarMascota, latitudMascota, longitudMascota,
             fotoMascota, fotoMascota2, fotoMascota3,
             estadoAdopcionMascota, fechaPublicacionMascota, idUsuarioFK1)
            VALUES (:nombre, :especie, :sexo, :edad, :raza, :descripcion, :lugar,
                    :lat, :lon, :foto1, :foto2, :foto3, :estado, :fecha, :idUsuario)");

        $stmt->bindParam(":nombre",      $nombre);
        $stmt->bindParam(":especie",     $especie);
        $stmt->bindParam(":sexo",        $sexo);
        $stmt->bindParam(":edad",        $edad);
        $stmt->bindParam(":raza",        $raza);
        $stmt->bindParam(":descripcion", $descripcion);
        $stmt->bindParam(":lugar",       $lugar);
        $stmt->bindParam(":lat",         $latitud);
        $stmt->bindParam(":lon",         $longitud);
        $stmt->bindParam(":foto1",       $foto1, PDO::PARAM_LOB);
        $stmt->bindParam(":foto2",       $foto2, PDO::PARAM_LOB);
        $stmt->bindParam(":foto3",       $foto3, PDO::PARAM_LOB);
        $stmt->bindParam(":estado",      $estado);
        $stmt->bindParam(":fecha",       $fechaPublicacion);
        $stmt->bindParam(":idUsuario",   $idUsuario);
        $stmt->execute();

        $idMascota = $conn->lastInsertId();

        // ── Comprobar alertas coincidentes — PREPARED STATEMENT (fix inyección SQL) ──
        $stmtAlertas = $conn->prepare("
            SELECT a.*, u.emailUsuario, u.nombreUsuario
            FROM Alerta a
            JOIN Usuario u ON u.idUsuario = a.idUsuarioFK
            WHERE a.idUsuarioFK != :uid
        ");
        $stmtAlertas->execute([":uid" => $idUsuario]);
        $alertas = $stmtAlertas->fetchAll(PDO::FETCH_ASSOC);

        foreach ($alertas as $alerta) {
            $coincide = true;

            if ($alerta["especie"] && $alerta["especie"] !== $especie) $coincide = false;
            if ($alerta["sexo"]    && $alerta["sexo"]    !== $sexo)    $coincide = false;
            if ($alerta["estado"]  && $alerta["estado"]  !== $estado)  $coincide = false;

            // Comprobar radio si tiene coordenadas
            if ($coincide && $alerta["latitud"] && $latitud) {
                $lat1 = deg2rad($alerta["latitud"]);
                $lat2 = deg2rad($latitud);
                $dLat = $lat2 - $lat1;
                $dLon = deg2rad($longitud - $alerta["longitud"]);
                $a = sin($dLat/2) * sin($dLat/2) +
                     cos($lat1) * cos($lat2) *
                     sin($dLon/2) * sin($dLon/2);
                $distancia = 6371 * 2 * asin(sqrt($a));
                if ($distancia > $alerta["radioKm"]) $coincide = false;
            } elseif ($coincide && $alerta["municipio"] && $municipio) {
                if ($alerta["municipio"] !== $municipio) $coincide = false;
            }

            if (!$coincide) continue;

            // Guardar notificación
            $msgNoti  = "Nueva publicación que coincide con tu alerta: {$nombre} ({$especie}) en {$lugar}";
            $stmtNoti = $conn->prepare("INSERT INTO Notificacion
                (idUsuarioFK, idMascotaFK, idAlertaFK, mensaje)
                VALUES (:uid, :mid, :aid, :msg)");
            $stmtNoti->execute([
                ":uid" => $alerta["idUsuarioFK"],
                ":mid" => $idMascota,
                ":aid" => $alerta["idAlerta"],
                ":msg" => $msgNoti
            ]);

            // Enviar email
            if (!empty($alerta["emailUsuario"])) {
                try {
                    $mail = crearMailer();
                    $mail->addAddress($alerta["emailUsuario"], $alerta["nombreUsuario"]);
                    $mail->isHTML(true);
                    $mail->Subject = "🐾 Adopta — Nueva publicación que coincide con tu alerta";
                    $mail->Body = "
                        <div style='font-family:Arial,sans-serif;max-width:600px;margin:0 auto;'>
                          <div style='background:#a7c7e7;padding:20px;border-radius:10px 10px 0 0;'>
                            <h2 style='color:#fff;margin:0;'>🐾 Adopta</h2>
                          </div>
                          <div style='padding:24px;background:#f8f9fb;border-radius:0 0 10px 10px;'>
                            <p>Hola <strong>{$alerta['nombreUsuario']}</strong>,</p>
                            <p>Se ha publicado un nuevo animal que coincide con una de tus alertas:</p>
                            <div style='background:#fff;border-radius:10px;padding:16px;margin:16px 0;border-left:4px solid #a7c7e7;'>
                              <p style='margin:0;'><strong>{$nombre}</strong> — {$especie} {$sexo}</p>
                              <p style='margin:4px 0 0;color:#888;font-size:0.9rem;'>📍 {$lugar}</p>
                              <p style='margin:4px 0 0;color:#888;font-size:0.9rem;'>Estado: {$estado}</p>
                            </div>
                            <a href='" . APP_URL . "/mascota.html?id={$idMascota}'
                               style='display:inline-block;background:#a7c7e7;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:bold;'>
                              Ver publicación
                            </a>
                            <p style='margin-top:24px;font-size:0.8rem;color:#aaa;'>
                              Puedes gestionar tus alertas desde tu panel en Adopta.
                            </p>
                          </div>
                        </div>";
                    $mail->send();
                } catch (\Exception $e) {
                    error_log("Error email alerta: " . $e->getMessage());
                }
            }
        }

        echo "success";
    } catch (PDOException $e) {
        error_log("Error publicar.php: " . $e->getMessage());
        echo "Error al guardar la publicación.";
    }
}
?>
