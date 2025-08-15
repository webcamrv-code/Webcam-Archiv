<?php
// Ziel-URL des Bildes
$imageUrl = "http://84.118.54.46/motion/-snapshot.jpg";


// Zielpfad im lokalen Ordner
$saveDir = "/home/ravensb1/public_html/bilder/";

// Ordner erstellen, falls er nicht existiert
if (!is_dir($saveDir)) {
    mkdir($saveDir, 0755, true);
}

// Zeitstempel im Dateinamen (Format: JJJJMMTT_HHMMSS)
$timestamp = date("Ymd_His");
$fileName = "snapshot_" . $timestamp . ".jpg";
$savePath = $saveDir . $fileName;

// Bild herunterladen und speichern
$imageData = file_get_contents($imageUrl);
if ($imageData === false) {
    echo "Fehler beim Herunterladen des Bildes.";
    exit;
}

file_put_contents($savePath, $imageData);
echo "Bild erfolgreich gespeichert unter: " . $savePath;
?>
