const CURRENT_PATH = location.pathname.split('/').pop() || 'index.html';

function buildHeader() {
  const nav = [
    ['index.html', 'Home'],
    ['privatkunden.html', 'Privatkunden'],
    ['gewerbekunden.html', 'Gewerbekunden'],
    ['gartenservice.html', 'Gartenservice'],
    ['kontakt.html', 'Kontakt']
  ];

  const active = (file) => CURRENT_PATH === file || (CURRENT_PATH === '' && file === 'index.html');
  const navLinks = nav
    .map(([href, label]) => `<a href="${href}" class="${active(href) ? 'active' : ''}">${label}</a>`)
    .join('');

  return `
  <header class="site-header">
    <div class="container nav-bar">
      <a href="index.html" class="brand" aria-label="Rühl & Rein Startseite">
        <div class="logo-box">✦</div>
        <div class="brand-text">
          <strong>Rühl & Rein</strong>
          <span>GEBÄUDEREINIGUNG</span>
        </div>
      </a>

      <nav class="nav-links" aria-label="Hauptnavigation">${navLinks}</nav>

      <div class="nav-cta">
        <a class="btn btn-primary" href="kontakt.html">Angebot anfordern</a>
      </div>

      <button class="mobile-toggle" aria-label="Menü öffnen" aria-expanded="false" aria-controls="mobileMenu" id="mobileToggle">☰</button>
    </div>

    <div class="container mobile-menu" id="mobileMenu">
      ${nav.map(([href, label]) => `<a href="${href}" class="${active(href) ? 'active' : ''}">${label}</a>`).join('')}
      <a class="btn btn-primary full" href="kontakt.html" style="margin-top:10px">Angebot anfordern</a>
    </div>
  </header>`;
}

function buildFooter() {
  return `
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <div>
          <a href="index.html" class="brand" style="color:#fff;margin-bottom:18px;display:inline-flex">
            <div class="logo-box" style="background:rgba(255,255,255,.1)">✦</div>
            <div class="brand-text">
              <strong style="color:#fff">Rühl & Rein</strong>
              <span style="color:rgba(255,255,255,.72)">GEBÄUDEREINIGUNG</span>
            </div>
          </a>
          <p>Ihr zuverlässiger Partner für professionelle <strong>Gebäudereinigung in Viersen</strong> und Umgebung. Wir bieten Reinigungsservice für Privatkunden, Gewerbekunden und Außenanlagen.</p>
        </div>

        <div>
          <h3>Leistungen</h3>
          <p><a href="privatkunden.html">Wohnungsreinigung</a></p>
          <p><a href="gewerbekunden.html">Büro- &amp; Treppenhausreinigung</a></p>
          <p><a href="gartenservice.html">Gartenservice &amp; Außenanlagen</a></p>
          <p><a href="kontakt.html">Kostenloses Angebot</a></p>
        </div>

        <div>
          <h3>Kontakt</h3>
          <p><a href="tel:+4917655727074">+49 176 55727074</a></p>
          <p><a href="mailto:kontakt@ruehl-rein.com">kontakt@ruehl-rein.com</a></p>
          <p>Gerhart-Hauptmann-Str. 8<br>41747 Viersen</p>
        </div>
      </div>

      <div class="footer-bottom">
        <small>© <span id="year"></span> Rühl & Rein Gebäudereinigung NRW. Alle Rechte vorbehalten.</small>
        <div style="display:flex;gap:18px;flex-wrap:wrap">
          <a href="datenschutz.html">Datenschutz</a>
          <a href="impressum.html">Impressum</a>
          <a href="#" onclick="CCM.openWidget(); return false;">Cookie Einstellungen</a>
        </div>
      </div>
    </div>
  </footer>`;
}

document.addEventListener('DOMContentLoaded', () => {
  const headerTarget = document.getElementById('site-header');
  const footerTarget = document.getElementById('site-footer');

  if (headerTarget) headerTarget.innerHTML = buildHeader();
  if (footerTarget) footerTarget.innerHTML = buildFooter();

  const menuBtn = document.getElementById('mobileToggle');
  const mobileMenu = document.getElementById('mobileMenu');

  if (menuBtn && mobileMenu) {
    menuBtn.addEventListener('click', () => {
      const isOpen = mobileMenu.classList.toggle('show');
      menuBtn.textContent = isOpen ? '✕' : '☰';
      menuBtn.setAttribute('aria-expanded', String(isOpen));
    });
  }

  const year = document.getElementById('year');
  if (year) year.textContent = new Date().getFullYear();
  
});
