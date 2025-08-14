# Webcam-Archiv
Ein modernes PHP-Skript zur Archivierung und Darstellung von Webcam-Bildern mit Wetterdaten, Mondphase, Galerie und Statistik.
# 📸 Webcam Archiv

Ein modernes PHP-Skript zur Archivierung und Darstellung von Webcam-Bildern mit Wetterdaten, Mondphase, Galerie und Statistik.

## 🔧 Funktionen

- 📡 Live-Webcam-Anzeige mit Statusprüfung
- 🌤 Wetterdaten via OpenWeatherMap API
- 🌙 Mondphasenberechnung
- 🖼 Galerie mit Paginierung (10 Bilder pro Seite)
- 🔍 Lightbox-Vorschau beim Klick auf Bilder
- 📊 Statistikseite mit Bildanzahl, Speicherverbrauch und Tagestrends
- 🎨 Modernes Design in Blau, Schwarz und Weiß mit Bootstrap Icons

## 🚀 Installation

1. PHP 7.4 oder höher installieren
2. Skript in ein Verzeichnis auf dem Webserver kopieren
3. Ordner `bilder/` erstellen und mit `.jpg`-Dateien füllen
4. OpenWeatherMap API-Key eintragen:
   ```php
   $api_key = 'DEIN_API_KEY_HIER';
