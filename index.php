<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
?>
<!doctype html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <title>PHP Image Toolkit</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
    <link rel="stylesheet" href="css/common.css">
</head>
<body>

<h1>PHP Image Toolkit</h1>
<p>Wählen Sie ein Bildbearbeitungs-Tool aus:</p>

<div class="tool-grid">

    <a class="tool-card" href="Massen-Bildkonverter.php">
        <h2>Bilder skalieren</h2>
        <p>Mehrere Bilder hochladen, skalieren und als ZIP herunterladen.</p>
    </a>

    <a class="tool-card" href="Format converter.php">
        <h2>Format konvertieren</h2>
        <p>Bilder in JPEG, PNG, WebP oder AVIF umwandeln.</p>
    </a>

    <a class="tool-card" href="Wasserzeichen_Tool.php">
        <h2>Wasserzeichen hinzufügen</h2>
        <p>Mehrere JPG-Bilder mit einem PNG-Wasserzeichen versehen.</p>
    </a>

</div>

</body>
</html>