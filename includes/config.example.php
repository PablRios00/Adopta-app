<?php
// ============================================================
// config.example.php — PLANTILLA de referencia (sí se sube a git)
//
// Copia este archivo como "config.php" en cada entorno (local o
// servidor de producción) y rellena los valores reales ahí.
// El "config.php" real NUNCA se sube a git (ver .gitignore).
// ============================================================

// ── Base de datos ──
define('DB_HOST',     'localhost');
define('DB_USER',     'TU_USUARIO_MYSQL');
define('DB_PASSWORD', 'TU_CONTRASENA_MYSQL');
define('DB_NAME',     'TU_BASE_DE_DATOS');

// ── Email (PHPMailer) ──
define('MAIL_HOST',     'smtp.gmail.com');
define('MAIL_PORT',     587);
define('MAIL_USER',     'tu-correo@gmail.com');
define('MAIL_PASSWORD', 'TU_CONTRASENA_DE_APLICACION_DE_GMAIL');
define('MAIL_FROM',     'tu-correo@gmail.com');
define('MAIL_FROM_NAME','Adopta 🐾');

// ── URL base de la app ──
// LOCAL:      http://localhost/adopciones/public
// PRODUCCIÓN: https://adopta.pablorios.eu
define('APP_URL', 'https://adopta.pablorios.eu');

// ── Límites de imágenes ──
define('IMG_MAX_SIZE_MB', 5);
define('IMG_MAX_SIZE_BYTES', 5 * 1024 * 1024);
define('IMG_TIPOS_PERMITIDOS', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
