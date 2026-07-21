// ============================================================
// FOOTER COMÚN — se inyecta en todas las páginas
// ============================================================
function inyectarFooter() {
  if (document.querySelector('.footer-adopciones')) return;

  const footer = document.createElement('footer');
  footer.className = 'footer-adopciones';
  footer.innerHTML = `
    <div class="container">
      <div class="row g-4 pb-3">
        <div class="col-12 col-md-3">
          <div class="footer-brand">
            <img src="assets/logo.png" alt="Logo"
                 style="width:28px;height:28px;object-fit:contain;filter:brightness(10);" />
            Adopta
          </div>
          <p class="footer-desc">
            Conectamos animales que necesitan un hogar con personas dispuestas a dárselo.
            Juntos hacemos un mundo mejor 🐾
          </p>
        </div>
        <div class="col-6 col-md-2">
          <div class="footer-titulo">Navegación</div>
          <a href="index.html">Inicio</a>
          <a href="publicar.html">Publicar animal</a>
          <a href="favoritos.html">Mis favoritos</a>
          <a href="mensajes.html">Mensajes</a>
        </div>
        <div class="col-6 col-md-2">
          <div class="footer-titulo">Mi cuenta</div>
          <a href="perfil.html">Mi perfil</a>
          <a href="publicaciones.html">Mis publicaciones</a>
          <a href="alertas.html">Mis alertas</a>
        </div>
        <div class="col-6 col-md-2">
          <div class="footer-titulo">Legal</div>
          <a href="privacidad.html">Política de privacidad</a>
          <a href="aviso-legal.html">Aviso legal</a>
          <a href="terminos.html">Términos de uso</a>
        </div>
        <div class="col-12 col-md-3">
          <div class="footer-titulo">¿Encontraste un animal?</div>
          <p class="footer-info-text">
            Si has encontrado un animal perdido o abandonado, comprueba si lleva chip
            consultando con un veterinario o la policía local antes de publicarlo.
            Tu ayuda puede cambiar su vida. 💙
          </p>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      © 2026 Adopta — Proyecto Integrado · Desarrollo de Aplicaciones Web
    </div>
  `;

  document.body.appendChild(footer);
}

inyectarFooter();