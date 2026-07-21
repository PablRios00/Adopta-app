<?php
require_once("../../includes/conexion.php");
header('Content-Type: application/json; charset=utf-8');

// ── Normalizar texto: quita tildes, pasa a minúsculas ──
function normalizarTexto($texto) {
    $texto = mb_strtolower(trim($texto), 'UTF-8');
    $desde = ['á','é','í','ó','ú','ü','ñ','à','è','ì','ò','ù','â','ê','î','ô','û','ä','ë','ï','ö','ü'];
    $hasta = ['a','e','i','o','u','u','n','a','e','i','o','u','a','e','i','o','u','a','e','i','o','u'];
    return str_replace($desde, $hasta, $texto);
}

$busqueda   = trim($_GET["busqueda"]   ?? "");
$especie    = trim($_GET["especie"]    ?? "");
$sexo       = trim($_GET["sexo"]       ?? "");
$estado     = trim($_GET["estado"]     ?? "");
$lat        = isset($_GET["lat"])        && $_GET["lat"]        !== "" ? floatval($_GET["lat"])        : null;
$lon        = isset($_GET["lon"])        && $_GET["lon"]        !== "" ? floatval($_GET["lon"])        : null;
$radioKm    = isset($_GET["radio"])      && $_GET["radio"]      !== "" ? floatval($_GET["radio"])      : null;
$latUsuario = isset($_GET["latUsuario"]) && $_GET["latUsuario"] !== "" ? floatval($_GET["latUsuario"]) : null;
$lonUsuario = isset($_GET["lonUsuario"]) && $_GET["lonUsuario"] !== "" ? floatval($_GET["lonUsuario"]) : null;

$where  = [];
$params = [];

if ($busqueda !== "") {
    $busquedaNorm = normalizarTexto($busqueda);
    $like         = "%" . $busqueda     . "%";
    $likeNorm     = "%" . $busquedaNorm . "%";

    // Busca tanto con el texto original como con la versión normalizada
    // CONVERT quita tildes en MySQL para comparación insensible a acentos
    $where[] = "(
        m.nombreMascota      LIKE :b1 OR m.nombreMascota      LIKE :b1n
        OR m.razaMascota     LIKE :b2 OR m.razaMascota     LIKE :b2n
        OR m.descripcionMascota LIKE :b3 OR m.descripcionMascota LIKE :b3n
        OR m.lugarMascota    LIKE :b4 OR m.lugarMascota    LIKE :b4n
        OR CONVERT(m.nombreMascota      USING utf8mb4) COLLATE utf8mb4_general_ci LIKE :b5
        OR CONVERT(m.razaMascota        USING utf8mb4) COLLATE utf8mb4_general_ci LIKE :b6
        OR CONVERT(m.descripcionMascota USING utf8mb4) COLLATE utf8mb4_general_ci LIKE :b7
        OR CONVERT(m.lugarMascota       USING utf8mb4) COLLATE utf8mb4_general_ci LIKE :b8
    )";
    $params[":b1"]  = $like;
    $params[":b1n"] = $likeNorm;
    $params[":b2"]  = $like;
    $params[":b2n"] = $likeNorm;
    $params[":b3"]  = $like;
    $params[":b3n"] = $likeNorm;
    $params[":b4"]  = $like;
    $params[":b4n"] = $likeNorm;
    $params[":b5"]  = $like;
    $params[":b6"]  = $like;
    $params[":b7"]  = $like;
    $params[":b8"]  = $like;
}

if ($especie !== "") { $where[] = "m.especieMascota = :especie";       $params[":especie"] = $especie; }
if ($sexo    !== "") { $where[] = "m.sexoMascota = :sexo";             $params[":sexo"]    = $sexo; }
if ($estado  !== "") { $where[] = "m.estadoAdopcionMascota = :estado"; $params[":estado"]  = $estado; }

$usaRadio            = ($lat !== null && $lon !== null && $radioKm !== null);
$usaDistanciaUsuario = (!$usaRadio && $latUsuario !== null && $lonUsuario !== null);

if ($usaRadio) {
    $where[] = "m.latitudMascota IS NOT NULL AND m.longitudMascota IS NOT NULL";
}

$selectDistancia = "";
$orderBy         = "m.fechaPublicacionMascota DESC";

if ($usaRadio) {
    $selectDistancia = ", (6371 * ACOS(
        COS(RADIANS(:lat1)) * COS(RADIANS(m.latitudMascota)) *
        COS(RADIANS(m.longitudMascota) - RADIANS(:lon1)) +
        SIN(RADIANS(:lat1)) * SIN(RADIANS(m.latitudMascota))
    )) AS distanciaKm";
    $params[":lat1"] = $lat;
    $params[":lon1"] = $lon;
    $orderBy = "distanciaKm ASC";
} elseif ($usaDistanciaUsuario) {
    $selectDistancia = ", (6371 * ACOS(
        COS(RADIANS(:latU)) * COS(RADIANS(m.latitudMascota)) *
        COS(RADIANS(m.longitudMascota) - RADIANS(:lonU)) +
        SIN(RADIANS(:latU)) * SIN(RADIANS(m.latitudMascota))
    )) AS distanciaKm";
    $params[":latU"] = $latUsuario;
    $params[":lonU"] = $lonUsuario;
    $orderBy = "CASE WHEN m.latitudMascota IS NULL THEN 1 ELSE 0 END, distanciaKm ASC";
}

$sql = "SELECT m.idMascota, m.nombreMascota, m.especieMascota, m.sexoMascota,
               m.edadMascota, m.razaMascota, m.lugarMascota,
               m.latitudMascota, m.longitudMascota,
               m.estadoAdopcionMascota, m.fechaPublicacionMascota,
               m.adoptado, m.idUsuarioFK1,
               u.nombreUsuario AS nombrePublicador
               $selectDistancia
        FROM Mascota m
        JOIN Usuario u ON u.idUsuario = m.idUsuarioFK1";

if (!empty($where)) $sql .= " WHERE " . implode(" AND ", $where);

if ($usaRadio) {
    $sql .= " HAVING distanciaKm <= :radio";
    $params[":radio"] = $radioKm;
}

$sql .= " ORDER BY $orderBy";

try {
    $stmt = $conn->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>