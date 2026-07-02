<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/filename_functions.inc.php");
require("includes/image_functions.inc.php");
require("includes/upload_functions.inc.php");
require("includes/validation_functions.inc.php");
require("includes/zip_functions.inc.php");
require("includes/batch_function.inc.php");


$msg = "";
$msg2 = "";
$msg3 = "";
$watermarkedFiles = [];

$maxFiles = 10; // Maximale Anzahl an Dateien, die hochgeladen werden können

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_FILES["images"]["name"][0]) && !empty($_FILES["watermark"]["name"]) ) {
        $opacity = ($_POST["opacity"] ?? "") === "" ? 50 : $_POST["opacity"];
        $valOpacity = validateOpacity($opacity);
            if($valOpacity['success'] === true) {
                $o = $valOpacity["opacity"];
                $f = $_FILES["images"];
                $fileCount = count($f["name"]);
                    if($fileCount <= $maxFiles) {
                        $w = $_FILES["watermark"];
                        $watermarkName = $w["name"];
                        $watermarkTmpName = $w["tmp_name"];
                            $position = validatePosition($_POST["position"] ?? "bottom-right");
                            $fino = finfo_open(FILEINFO_MIME_TYPE);
                            $watermarkMimeType = finfo_file($fino, $watermarkTmpName);
                            finfo_close($fino); 
                                if ($watermarkMimeType === "image/png") {
                                    $batch = createBatchPath('uploads_bildconverter');

                                    for ($i = 0; $i < $fileCount; $i++) {

                                        $file = getUploadedFileByIndex($f, $i);
                                        $filename = $file["name"];
                                            $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                            $mimeType = finfo_file($finfo, $file["tmp_name"]);
                                            finfo_close($finfo);
                                        if ($mimeType === "image/jpeg") {
                                            $newFilename = createSafeFilename($filename, "jpeg");

                                            $outputPath = $batch["outputDir"] . $newFilename;
                                            $previewPath = $batch["publicOutputDir"] . $newFilename;
                                            $inputPath = $file["tmp_name"];

                                                    $ok = addWatermark(
                                                        $inputPath,
                                                        $watermarkTmpName,
                                                        $outputPath,
                                                        $position,
                                                        $o
                                                    );
                                                        if ($ok) {
                                                            $watermarkedFiles[] = $outputPath;

                                                            $msg .= '<p class="success">Wasserzeichen erfolgreich zu ' . htmlspecialchars($filename) . ' hinzugefügt.</p>';
                                                            $msg2 .= '<div class="preview-card">';
                                                            $msg2 .= '<h3>Preview Image - ' . htmlspecialchars($newFilename) . '</h3>';
                                                            $msg2 .= '<img src="' . htmlspecialchars($previewPath) . '" alt="Bild mit Wasserzeichen">';
                                                            $msg2 .= '<p><a href="' . htmlspecialchars($previewPath) . '" target="_blank" rel="noopener noreferrer">Bild in Originalgröße öffnen</a></p>';
                                                            $msg2 .= '</div>';
                                                        } else {
                                                            $msg .= '<p class="error">Fehler beim Hinzufügen des Wasserzeichens zu <strong>' . htmlspecialchars($filename) . '</strong>.</p>';
                                                        }
                                        }
                                        else {
                                            $msg .= '<p class="error"><strong>' . htmlspecialchars($filename) . '</strong>: Dieser Dateityp ist nicht erlaubt. Bitte laden Sie JPG-Dateien hoch.</p>';
                                        }
                                    }
                        }
                        else {
                            $msg = '<p class="error">Bitte laden Sie eine PNG-Datei als Wasserzeichen hoch.</p>';
                        }

                    }
                    else {
                        $msg = '<p class="error">Bitte laden Sie maximal ' . $maxFiles . ' Bilder hoch.</p>';
                        }
            }
            else {
                $msg = '<p class="error">' . htmlspecialchars($valOpacity['message']) . '</p>';
            }

    }
    else {
        $msg = '<p class="error">Bitte wählen Sie mindestens eine Bilddatei für das Bild und ein Wasserzeichen aus.</p>';
        }
}

if (!empty($watermarkedFiles)) {
    $zipPath = $batch["batchDir"] . $batch["batchId"] . '.zip';

    $zipCreated = createZip($watermarkedFiles, $zipPath);

        if ($zipCreated) {

        $msg3 = '<p><a class="download-button" href="download_zip.php?batch=' . urlencode($batch["batchId"]) . '">Download ZIP-Datei</a></p>';
        }
        else {
            $msg3 = '<p class="error">Die ZIP-Datei konnte nicht erstellt werden.</p>';
        }
}

?>
<!doctype html>
<html lang="de">
	<head>
		<title>Wasserzeichen hinzufügen</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
        <link rel="stylesheet" href="css/common.css">
	</head>
	<body>
    <?php require("includes/header.inc.php"); ?>
        <h1>Wasserzeichen zu Bildern hinzufügen</h1>
            <form method="post" enctype="multipart/form-data">
                <label>
                    Bilder auswählen (maximal <?php echo $maxFiles; ?>, nur JPG):
                    <input type="file" name="images[]" multiple accept="image/jpeg">
                </label>

                <label>
                    Wasserzeichen / Logo auswählen:
                    <input type="file" name="watermark" accept="image/png">
                </label>
                <label>
                    Position:
                    <select name="position">
                        <option value="bottom-right">Rechts unten</option>
                        <option value="bottom-left">Links unten</option>
                        <option value="top-right">Rechts oben</option>
                        <option value="top-left">Links oben</option>
                        <option value="center">Mitte</option>
                    </select>
                </label>
                <label>
                    Transparenz:
                    <input type="number" name="opacity" min="1" max="100" value="50">
                </label>
                <input type="submit" value="Wasserzeichen hinzufügen">
            </form>
		    <?php echo($msg3); ?>
            <?php echo($msg); ?>
            <?php if (!empty($msg2)): ?>
                <h2>Vorschau der Bilder mit Wasserzeichen</h2>
            <?php echo($msg2); ?>
            <?php endif; ?>
	</body>
</html>