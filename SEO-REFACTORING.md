# SEO-Refactoring-Dokumentation

Stand: 2026-04-22

## Ausgangsanalyse

Zentrale Projektdateien:
- `public/*.html`: statische Seiten, Inhalte, Navigation, SEO-Metadaten.
- `public/assets/css/style.css`: komplettes visuelles System und responsive Regeln.
- `public/assets/js/script.js`: bisher Header-/Footer-Injection, jetzt Interaktion und Tracking-Hooks.
- `public/contact.php`: serverseitige Formularverarbeitung per PHPMailer.
- `config.php`: SMTP-Konfiguration außerhalb des Public-Verzeichnisses.

Problematische Punkte vor dem Refactor:
- Header und Footer wurden per JavaScript injiziert und waren dadurch unnötig fragil.
- Canonicals, Robots-Meta, Open Graph, Twitter Card, JSON-LD, `robots.txt` und `sitemap.xml` fehlten.
- `gewerbekunden.html` bündelte Büroreinigung und Treppenhausreinigung auf einer URL.
- Utility-Seiten hatten keine saubere Indexierungsstrategie.
- Kontaktformular hatte wenige UX-Attribute und keine saubere Lead-Messbasis.
- Interne Verlinkung, lokale Signale, Trust-Content und semantische Bilder waren zu schwach.

## Umgesetzte Struktur

Indexierbare Kernseiten:
- `/`
- `/privatkunden.html`
- `/gewerbekunden.html`
- `/bueroreinigung.html`
- `/treppenhausreinigung.html`
- `/gartenservice.html`
- `/kontakt.html`

Nicht für organische Rankings vorgesehene Utility-Seiten:
- `/impressum.html`: `noindex,follow`
- `/datenschutz.html`: `noindex,follow`
- `/contact-success.html`: `noindex,follow`, canonical auf `/kontakt.html`
- `/404.html`: `noindex,follow`

## Wichtigste Änderungen

Technik:
- Statische Header/Footer direkt in die HTML-Seiten integriert.
- JavaScript reduziert auf mobiles Menü, Jahreszahl, Cookie-Link und Tracking-Hooks.
- `404.html` ergänzt und per `.htaccess` als ErrorDocument vorbereitet.
- `robots.txt` und `sitemap.xml` erstellt.
- Favicon und Social-Card-Basis unter `public/assets/img/` ergänzt.

SEO:
- Titles, Meta Descriptions, Canonicals und Robots-Meta für alle HTML-Seiten standardisiert.
- Open Graph und Twitter Card für relevante Seiten ergänzt.
- JSON-LD für `ProfessionalService`, `Service`, `BreadcrumbList`, `WebSite` und `ContactPage` passend zum Seiteninhalt ergänzt.
- Sitemap enthält nur indexierbare Kernseiten.

Informationsarchitektur:
- `gewerbekunden.html` ist jetzt ein Gewerbe-Hub.
- `bueroreinigung.html` und `treppenhausreinigung.html` wurden als eigene Landingpages angelegt.
- Interne Links führen von Startseite, Hub, Footer und Kontextblöcken gezielt auf die passenden Leistungsseiten.

Content und Local SEO:
- H1/H2-Struktur pro Seite geschärft.
- Lokaler Kontext Viersen, Kreis Viersen und direkte Umgebung sichtbarer gemacht.
- Prozess-, Trust-, FAQ- und Servicegebiet-Abschnitte ergänzt.
- Gartenservice klar als Außenanlagenpflege rund um Immobilien positioniert, nicht als schwerer Garten- und Landschaftsbau.

Conversion und UX:
- Kontaktformular um Leistungswahl und Objektort ergänzt.
- `autocomplete`, `inputmode`, Feldhinweise und `aria-live` für Statusmeldungen ergänzt.
- Mobile Sticky-Kontaktleiste für Anruf und Anfrage ergänzt.
- CTA-Labels präziser und weniger generisch formuliert.

Tracking-Vorbereitung:
- `window.dataLayer` wird vorbereitet.
- `window.rrTrack(eventName, params)` pusht Events in `dataLayer` und optional an `gtag`, falls später GA4 eingebunden wird.
- Vorbereitete Events:
  - `lead_form_submit` bei erfolgreichem Formularstatus
  - `click_phone`
  - `click_email`
  - `cta_click`
  - `view_service_page`

## Redirect-Hinweise

Es wurden keine bestehenden Kern-URLs entfernt. Daher sind für den aktuellen Refactor keine Pflicht-301-Redirects nötig.

Empfohlene optionale Redirects auf Serverebene:
- `/contact-success.html` -> `/kontakt.html` mit 301, wenn die alte Erfolgsseite nicht mehr direkt erreichbar sein soll.
- Falls später Clean URLs eingeführt werden, sollten `.html`-URLs dauerhaft weitergeleitet werden, z. B. `/bueroreinigung.html` -> `/bueroreinigung/`.

Die aktuelle `.htaccess` setzt nur:
- `ErrorDocument 404 /404.html`
- `Options -Indexes`

Hinweis: Der lokale PHP-Entwicklungsserver wertet `.htaccess` nicht aus. Der korrekte 404-Status muss deshalb nach Deployment auf dem Apache/Hostinger-Server geprüft werden.

## Externe To-dos

- GA4 Property und/oder GTM Container anlegen und echte ID einbauen.
- GA4 Key Events für `lead_form_submit`, `click_phone`, `click_email` und wichtige CTA-Klicks markieren.
- Google Search Console Domain-Property verifizieren.
- `https://ruehl-rein.com/sitemap.xml` in der Search Console einreichen.
- Google Business Profile prüfen: Kategorie, Servicegebiet, Leistungen, Öffnungszeiten, Fotos und Review-Prozess.
- Echte lokale Bilder produzieren und die externen Unsplash-Bilder schrittweise ersetzen.
- Social-Card idealerweise als echte `1200x630` PNG/WebP exportieren.
- Server-Live-QA nach Deployment: HTTPS, Statuscodes, 404, Canonicals, Sitemap-Abruf und Formularversand.
- Optional: reale Referenzen, Projektfotos oder Kundenstimmen ergänzen, sobald rechtlich und fachlich belastbar.
