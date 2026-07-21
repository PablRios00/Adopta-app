# 🐾 Adopta

Aplicación web full-stack de adopción de animales en España. A diferencia de los tablones de anuncios genéricos, Adopta incorpora **geolocalización real** para recomendar animales cercanos, un **sistema de alertas** que notifica al usuario cuando se publica un animal que coincide con sus criterios de búsqueda, y un **chat integrado** para que adoptante y publicador se comuniquen directamente.

Desarrollada como Proyecto Integrado del Ciclo Superior de Desarrollo de Aplicaciones Web (DAW), pensada desde el principio para escalar a un producto real.

🔗 **Demo en producción:** [adopta.pablorios.eu](https://adopta.pablorios.eu)
🔗 **Portfolio del autor:** [pablorios.eu](https://pablorios.eu)

---

## ✨ Funcionalidades

### Para usuarios
- **Registro y autenticación** con contraseñas cifradas (bcrypt vía `password_hash`/`password_verify`)
- **Publicación de animales** con hasta 3 fotos, geolocalización mediante mapa interactivo (Leaflet + Nominatim) y validación real de tipo MIME e imágenes
- **Buscador avanzado** tolerante a tildes y erratas, con normalización de texto
- **Filtro por ubicación y radio** usando la fórmula de Haversine para calcular distancias reales
- **Recomendaciones por cercanía** — el listado principal ordena los animales de más cercano a más lejano según la ubicación del usuario
- **Sistema de alertas** — guarda filtros de búsqueda y avisa (en la app y por email) cuando se publica algo que coincide
- **Chat en tiempo real** entre usuarios, vinculado a cada publicación
- **Carrusel de últimos perdidos** con rotación automática y navegación manual
- **Favoritos** para guardar publicaciones de interés
- **Galería de fotos** por publicación con miniaturas y navegación
- **Perfil editable** con foto, ubicación y opción de eliminar cuenta

### Técnicas y de arquitectura
- **Separación real entre lo público y lo privado**: todo lo que el navegador necesita vive en `public/`; la configuración y las credenciales viven fuera, inalcanzables desde una URL
- **Configuración centralizada** en `config.php` — un único archivo para credenciales y constantes, nunca subido a git
- **PDO con prepared statements** en todas las queries — sin interpolación directa de variables
- **Imágenes guardadas como `LONGBLOB` en MySQL** (no en el sistema de archivos), servidas mediante endpoints PHP dedicados
- **Validación de imágenes** con `finfo` (MIME real), `getimagesize` y límite de tamaño configurable
- **Rutas 100% relativas** en frontend — el mismo código funciona en local (XAMPP, bajo `/adopciones/`) y en producción (subdominio propio) sin tocar una sola línea
- **Footer y sidebar comunes** inyectados por JS — un solo punto de mantenimiento para el menú de navegación
- **Cumplimiento legal** — Política de privacidad (RGPD), Aviso legal (LSSI) y Términos de uso (Ley 7/2023 de Bienestar Animal)

---

## 🛠 Stack tecnológico

| Capa | Tecnología |
|---|---|
| Frontend | HTML5, CSS3, JavaScript ES6+ |
| Backend | PHP 8, PDO |
| Base de datos | MySQL 8 / MariaDB |
| Mapas y geolocalización | Leaflet 1.9 + Nominatim (OpenStreetMap) |
| Email transaccional | PHPMailer + Gmail SMTP |
| Control de versiones | Git / GitHub |
| Entorno local | XAMPP (Apache + PHP + MySQL) |
| Hosting de producción | Hostinger (subdominio de [pablorios.eu](https://pablorios.eu)) |

---

## 🗄 Modelo de base de datos

```
Usuario ──────── Mascota ──────── Adopcion
   │                │
   ├── Favoritos ───┘
   ├── Mensaje (emisor / receptor / mascota)
   ├── Alerta (filtros + coordenadas + radio)
   └── Notificacion (vinculada a Alerta + Mascota)
```

7 tablas con relaciones definidas mediante claves foráneas, con `ON DELETE CASCADE` donde corresponde para mantener la integridad al borrar usuarios o publicaciones.

---

## 📁 Estructura del proyecto

```
adopciones/
├── includes/                     # Configuración y conexión — NO accesible desde el navegador
│   ├── config.php                # ⚠️ Credenciales reales — nunca se sube a git
│   ├── config.example.php        # Plantilla sin credenciales, sí se sube a git
│   ├── conexion.php               # Conexión PDO
│   └── mailer.php                 # Configuración PHPMailer
├── phpmailer/                     # Librería PHPMailer — NO accesible desde el navegador
└── public/                        # Raíz web real (esto es lo único que el servidor expone)
    ├── assets/                    # CSS, JS compartido, logo
    ├── php/                       # ~28 endpoints PHP (API interna de la app)
    ├── index.html                 # Inicio: buscador, carrusel, recomendaciones
    ├── mascota.html                # Ficha de detalle de una publicación
    ├── publicar.html               # Formulario de publicación con mapa
    ├── mensajes.html                # Chat
    ├── perfil.html                  # Perfil del usuario
    ├── alertas.html                  # Gestión de alertas guardadas
    ├── favoritos.html
    ├── login.html / registro.html
    └── privacidad.html / aviso-legal.html / terminos.html
```

`public/` es intencionadamente la única carpeta que el servidor web expone —tanto en local como en producción— para que `includes/` y `phpmailer/` (con las credenciales y la lógica sensible) no sean alcanzables directamente por URL bajo ninguna circunstancia.

---

## 🚀 Instalación en local (XAMPP)

### Requisitos
- XAMPP (PHP 8.0+, MySQL 8.0+)
- Navegador moderno

### Pasos

**1. Clonar el repositorio**
```bash
git clone https://github.com/PablRios00/Adopta-app.git adopciones
```

**2. Copiar dentro de XAMPP**
```
C:\xampp\htdocs\adopciones\
```

**3. Crear la base de datos**

Importa el script SQL incluido (`adopcionesDB.sql`) desde phpMyAdmin, o ejecútalo directamente — crea las 7 tablas con sus relaciones.

**4. Configurar credenciales**

Copia `includes/config.example.php` como `includes/config.php` y rellena tus datos reales:
```php
define('DB_HOST',     'localhost');
define('DB_USER',     'tu_usuario');
define('DB_PASSWORD', 'tu_contraseña');
define('DB_NAME',     'adopcionesDB');

define('MAIL_USER',     'tu_email@gmail.com');
define('MAIL_PASSWORD', 'tu_contraseña_de_aplicación_gmail'); // 16 caracteres, no tu contraseña normal

define('APP_URL', 'http://localhost/adopciones/public');
```

**5. Instalar PHPMailer**

Descarga [PHPMailer](https://github.com/PHPMailer/PHPMailer), y copia el contenido de su carpeta `src/` dentro de `phpmailer/` en la raíz del proyecto.

**6. Arrancar Apache y MySQL desde el panel de XAMPP, y acceder a:**
```
http://localhost/adopciones/public/index.html
```

---

## 🌐 Despliegue en producción

El proyecto se despliega como subdominio independiente (`adopta.pablorios.eu`) con su propia base de datos MySQL en Hostinger, apuntando la raíz del subdominio directamente a la carpeta `public/`. El `includes/config.php` de producción se crea manualmente en el servidor (nunca vía git), con las credenciales reales de esa base de datos.

Al usar rutas 100% relativas en todo el frontend, el mismo código —sin ninguna modificación— funciona igual en local (bajo `/adopciones/`) y en producción (en la raíz del subdominio).

---

## 🔒 Seguridad

- `includes/config.php` está en `.gitignore` — nunca se sube al repositorio; `includes/config.example.php` es la plantilla pública sin credenciales
- Las contraseñas de usuario se almacenan cifradas con bcrypt, nunca en texto plano
- Todas las queries usan PDO con prepared statements — sin interpolación directa de variables (sin inyección SQL)
- Las imágenes se validan con MIME real (`finfo`), `getimagesize` y límite de tamaño configurable antes de guardarse
- Las sesiones verifican la propiedad de los recursos antes de permitir editar o eliminar

---

## 🗺 Roadmap

- [ ] Paginación / scroll infinito en el listado de publicaciones
- [ ] Verificación de email al registrarse
- [ ] Sistema de reportes de publicaciones
- [ ] Panel de administración
- [ ] App móvil (PWA)

---

## 👤 Autor

**Pablo Ríos González**
Técnico Superior en Desarrollo de Aplicaciones Web
📍 Sevilla, España · 📧 pabloriosglez@gmail.com
🔗 [github.com/PablRios00](https://github.com/PablRios00) · 🌐 [pablorios.eu](https://pablorios.eu)

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Consulta el archivo `LICENSE` para más detalles.
