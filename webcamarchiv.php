<?php
date_default_timezone_set('Europe/Berlin');

// Bildpfad definieren
$verzeichnis = 'bilder/';
$bilder = glob($verzeichnis . '*.jpg');

// Gesamtanzahl & Speicherplatz berechnen
$anzahl_bilder = count($bilder);
$gesamt_bytes = array_sum(array_map('filesize', $bilder));
$gesamt_mb = round($gesamt_bytes / (1024 * 1024), 2);

// Kamera-Status abfragen
$image_url = "https://ravensburg-webcam.de/snapshot.jpg";
$headers = @get_headers($image_url);
$cam_online = $headers && strpos($headers[0], '200 OK') !== false;

// Mondphase berechnen
function getMoonPhase($year, $month, $day) {
    $lp = 2551443;
    $date = mktime(0, 0, 0, $month, $day, $year);
    $new_moon = mktime(20, 35, 0, 1, 6, 2000);
    $phase = (($date - $new_moon) % $lp) / (float)$lp;

    if ($phase < 0.02 || $phase > 0.98) return "🌑 Neumond";
    elseif ($phase < 0.25) return "🌒 zunehmend";
    elseif ($phase < 0.27) return "🌓 erstes Viertel";
    elseif ($phase < 0.48) return "🌔 zunehmend";
    elseif ($phase < 0.52) return "🌕 Vollmond";
    elseif ($phase < 0.75) return "🌖 abnehmend";
    elseif ($phase < 0.77) return "🌗 letztes Viertel";
    else return "🌘 abnehmend";
}

$year = date("Y");
$month = date("n");
$day = date("j");
$mondphase = getMoonPhase($year, $month, $day);

// Sonneninfos
$heute = strtotime("today");
$breitengrad = 47.7833;
$längengrad = 9.6167;
$sonne = date_sun_info($heute, $breitengrad, $längengrad);

// Pagination vorbereiten
$bilder_pro_seite = 16;
$seite = isset($_GET['seite']) ? max(1, intval($_GET['seite'])) : 1;
$gesamt_seiten = ceil($anzahl_bilder / $bilder_pro_seite);

// Bilder sortieren (neueste zuerst)
usort($bilder, fn($a, $b) => filemtime($b) - filemtime($a));

// Bilder für aktuelle Seite auswählen
$start_index = ($seite - 1) * $bilder_pro_seite;
$bilder_aktuell = array_slice($bilder, $start_index, $bilder_pro_seite);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>📷 Webcam Ravensburg & Galerie</title>
    <style>
        body {
            font-family: sans-serif;
            background: #fff;
            text-align: center;
            padding: 30px;
            margin: 0;
        }
        h1, h2 {
            color: #333;
        }
        .status {
            padding: 10px;
            font-weight: bold;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 20px;
        }
        .online { background-color: #28a745; color: white; }
        .offline { background-color: #dc3545; color: white; }
        .live-image-container {
            display: inline-block;
            padding: 10px;
            border: 6px solid #ccc;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #fff, #fff);
            transition: transform 0.3s ease;
            margin: 20px auto;
        }
        .live-image-container:hover {
            transform: scale(1.03);
            box-shadow: 0 0 25px rgba(0,0,0,0.4);
        }
        .live-image-container img {
            width: 350px;
            height: auto;
            border-radius: 10px;
            border: 2px solid #333;
        }
        .info-row {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin: 30px 0;
            font-size: 1.1em;
        }
        .bild-wrapper {
            display: inline-block;
            margin: 15px;
            background: white;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 2px 2px 6px rgba(0,0,0,0.1);
        }
        .thumbnail {
            width: 160px;
            border: 2px solid #555;
            border-radius: 4px;
            transition: 0.3s;
            cursor: pointer;
        }
        .thumbnail:hover {
            transform: scale(1.05);
        }
        .datum {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        #lightbox {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.8);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }
        #lightbox img {
            max-width: 80%;
            max-height: 80%;
            border: 4px solid white;
            border-radius: 8px;
            box-shadow: 0 0 20px black;
        }
        .pagination {
            margin-top: 30px;
            font-size: 1.2em;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #007bff;
        }
        .pagination strong {
            margin: 0 5px;
            color: #333;
        }
    </style>
</head>
<body>
<hr>
<?php if ($cam_online): ?>
    <div class="live-image-container">
        <img src="<?= $image_url ?>" alt="Live Webcam Ravensburg">
    </div>
<?php else: ?>
    <p>❌ Die Webcam ist derzeit nicht erreichbar.</p>
<?php endif; ?>
<hr>
<div class="status <?= $cam_online ? 'online' : 'offline' ?>">
    <?= $cam_online ? '🟢 LIVE Kamera ON' : '🔴 Kamera OFF' ?>
</div>

<div class="info-row">
    <div>📅 <strong><?= date("d.m.Y") ?></strong></div>
    <div>🌙Mond <strong><?= $mondphase ?></strong></div>
    <div>🌅Sonnenaufgang <strong><?= date("H:i", $sonne['sunrise']) ?> Uhr</strong></div>
    <div>🌇Sonnenuntergang <strong><?= date("H:i", $sonne['sunset']) ?> Uhr</strong></div>
    <div>🖼Bilder gespeichert️ <strong><?= $anzahl_bilder ?> Bilder</strong></div>
    <div>📦Belegter Speicher <strong><?= $gesamt_mb ?> MB</strong></div>
</div>

<h1>🆕 Neueste Webcam-Bilder</h1>

<?php
foreach ($bilder_aktuell as $bild) {
    $datum = date("d.m.Y H:i:s", filemtime($bild));
    echo "<div class='bild-wrapper'>";
    echo "<img src='$bild' alt='Webcam-Bild' class='thumbnail' onclick='openLightbox(this.src)'>";
    echo "<div class='datum'>📸 $datum</div>";
    echo "</div>";
}
?>

<!-- Pagination -->
<div class="pagination">
    📄 Seiten:
    <?php
    for ($i = 1; $i <= $gesamt_seiten; $i++) {
        if ($i == $seite) {
            echo "<strong>[$i]</strong>";
        } else {
            echo "<a href='?seite=$i'>$i</a>";
        }
    }
    ?>
</div>

<!-- Lightbox -->
<div id="lightbox" onclick="this.style.display='none'">
    <img src="" alt="Großansicht">
</div>
<script>
    function openLightbox(src) {
        const lightbox = document.getElementById('lightbox');
        lightbox.style.display = 'flex';
        lightbox.querySelector('img').src = src;
    }
</script>
<footer class="bg-dark text-white text-center py-4">
  <div class="container">
    <p class="mb-1">© 2025 Ravensburg Webcam</p>
    <a href="https://ravensburg-webcam.de/" class="text-white text-decoration-none">Zur Startseite</a>
  </div>
</footer>


</body>
</html>
