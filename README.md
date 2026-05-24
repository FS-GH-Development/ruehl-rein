# Rühl & Rein Website

Aktueller Projektstand: 2026-05-24

Statische Website für Rühl & Rein Gebäudereinigung mit PHP-Kontaktformular.

## Inhalt

- Fokus auf Reinigungsleistungen: Wohnungsreinigung, Fensterreinigung, Büroreinigung und Treppenhausreinigung.
- Gartenservice ist aus Navigation, Sitemap und Leistungsseiten entfernt.
- `/gartenservice.html` wird per `.htaccess` auf die Startseite weitergeleitet.
- Canonical-Domain ist `https://ruehl-rein.com/` ohne `www`.
- Sitemap liegt unter `public/sitemap.xml`.
- Rechtliche Utility-Seiten wie Impressum und Datenschutz sind mit `noindex,follow` versehen.
- Erfolgreiche Kontaktformular-Anfragen landen auf `contact-success.html`. Diese Seite eignet sich als Google-Ads-Conversion-Seite für Angebotsanfragen.
- Google-Unternehmensprofil ist eingerichtet. Es wird kein unsicherer Google-Maps-Suchlink eingebunden; ein Profil-Link sollte erst ergänzt werden, wenn ein stabiler öffentlicher Link wirklich zuverlässig funktioniert.

## Projektstruktur

```text
public/                 Öffentlicher Webroot für Hostinger
public/assets/          CSS, JavaScript und lokale Bilder
public/contact.php      Kontaktformular-Verarbeitung
lib/phpmailer/          PHPMailer außerhalb des Webroots
config.example.php      Vorlage für lokale SMTP-Konfiguration
config.php              Lokale/produktive SMTP-Konfiguration, nicht im Git
```

## Lokal starten

```sh
php -S localhost:8000 -t public
```

Danach im Browser öffnen:

```text
http://localhost:8000
```

## Kontaktformular konfigurieren

`config.php` wird absichtlich nicht versioniert, weil dort SMTP-Zugangsdaten stehen.

Für eine neue Umgebung:

```sh
cp config.example.php config.php
```

Dann die SMTP-Werte in `config.php` eintragen.

## Deployment zu Hostinger

In `public_html/` gehören die Inhalte aus:

```text
public/
```

Zusätzlich müssen auf dem Server neben `public_html/` liegen:

```text
config.php
lib/phpmailer/
```

Nicht hochladen:

```text
.git/
backup/
.DS_Store
*.md
```

## Nach Änderungen prüfen

```sh
xmllint --noout public/sitemap.xml public/assets/img/social-card.svg
ruby -rjson -e 'Dir["public/*.html"].each { |file| File.read(file).scan(%r{<script type="application/ld\+json">(.*?)</script>}m).each { |match| JSON.parse(match[0]) } }; puts "JSON-LD OK"'
php -l public/contact.php
```

## Aktuelle Git-Hygiene

Das Repository enthält nur den aktuellen Website-Code und notwendige Laufzeitbibliotheken.
Lokale Backups, alte Audit-Notizen, Finder-Dateien und echte Zugangsdaten bleiben außerhalb von Git.
