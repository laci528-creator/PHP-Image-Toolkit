<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/message_functions.inc.php");
require("includes/filename_functions.inc.php");
require("includes/image_functions.inc.php");
require("includes/upload_functions.inc.php");
require("includes/validation_functions.inc.php");
require("includes/zip_functions.inc.php");


$msg = "";
$msg2 = "";
$msg3 = "";
$resizedFiles = [];

$maxFiles = 20;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_FILES["myUpload"]["name"][0]) && isset($_POST["neu_resolution"]) && $_POST["neu_resolution"] !== "") {
        $resolution = $_POST["neu_resolution"];
        $validationResult = validateResolution($resolution);
            if ($validationResult["success"] === true) {
                $neuResolution = $validationResult["resolution"];
                $f = $_FILES["myUpload"];
                $fileCount = count($f["name"]);

                if($fileCount <= $maxFiles) {
                    if (!is_dir('./output_image/')) {
                        mkdir('./output_image/', 0755, true);
                    }

                        for ($i = 0; $i < $fileCount; $i++) {   
                            $file = getUploadedFileByIndex($f, $i);
                            $filename = $file["name"];
                            $validation = validateUploadedImage($file);
                                if($validation['success'] === true)  { 

                                    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                                    $newFilename = createSafeFilename($filename, $extension);
                                    $newFilenameWithResolution = pathinfo($newFilename, PATHINFO_FILENAME) . "_" . $neuResolution . "." . $extension;

                                    $outputPath = './output_image/' . $newFilenameWithResolution;
                                    $inputPath = $file["tmp_name"];
                                    // ta($newFilenameWithResolution);

                                    $ok = Convert_Bild($inputPath, $neuResolution, $outputPath);

                                        if ($ok) {
                                            $resizedFiles[] = $outputPath;

                                            $imageSize = getimagesize($outputPath);
                                            $width = $imageSize[0] ?? 0;
                                            $height = $imageSize[1] ?? 0;

                                            $msg .= '<p class="success">Die Datei <strong>' . htmlspecialchars($newFilenameWithResolution) . '</strong> wurde erfolgreich auf ' . htmlspecialchars((string)$width) . ' x ' . htmlspecialchars((string)$height) . ' px skaliert.</p>';
                                                $msg2 .= '<div class="preview-card">';
                                                $msg2 .= '<h3>Preview Image - ' . htmlspecialchars($newFilenameWithResolution) . '</h3>';
                                                $msg2 .= '<p>Neue Bildgröße: ' . htmlspecialchars((string)$width) . ' × ' . htmlspecialchars((string)$height) . ' px</p>';
                                                $msg2 .= '<img src="./output_image/' . htmlspecialchars($newFilenameWithResolution) . '" alt="Skaliertes Bild">';
                                                $msg2 .= '<p><a href="./output_image/' . htmlspecialchars($newFilenameWithResolution) . '" target="_blank">Bild in Originalgröße öffnen</a></p>';
                                                $msg2 .= '</div>';
                                        } else {
                                            $msg .= '<p class="error">Die Datei <strong>' . htmlspecialchars($filename) . '</strong> wurde hochgeladen, aber die Konvertierung ist fehlgeschlagen.</p>';
                                        }

                                }
                                else {
                                    $msg .= '<p class="error">Fehler bei der Datei <strong>' . htmlspecialchars($filename) . '</strong>: ' . htmlspecialchars($validation["message"]) . '</p>';
                                }
                        }
                }
                else {
                    $msg = '<p class="error">Bitte laden Sie maximal ' . $maxFiles . ' Bilder hoch.</p>';
                }

            } 
            else {
                $msg = '<p class="error">' . htmlspecialchars($validationResult["message"]) . '</p>';
            }
    }
    else {
        $msg = '<p class="error">Bitte laden Sie mindestens eine Datei hoch und geben Sie die gewünschte Auflösung an.</p>';
    }
}

if (!empty($resizedFiles)) {
    $zipName = 'resized_images_' . bin2hex(random_bytes(6)) . '.zip';
    $zipPath = './zip/' . $zipName;

    $zipCreated = createZip($resizedFiles, $zipPath);

        if ($zipCreated) {

        $msg3 = '<p><a class="download-button" href="' . htmlspecialchars($zipPath) . '" download>Alle Bilder als ZIP herunterladen</a></p>';
        }
        else {
            $msg3 = '<p class="error">Die ZIP-Datei konnte nicht erstellt werden.</p>';
        }
}

?>
<!doctype html>
<html lang="de">
	<head>
		<title>Bilder skalieren</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
        <link rel="stylesheet" href="css/common.css">
	</head>
	<body>
        <?php require("includes/header.inc.php"); ?>
		<h1>Bildgröße für mehrere Dateien ändern</h1>
		
		<form method="post" enctype="multipart/form-data">
			<label>
				Bitte wählen Sie maximal 20 Bilddateien aus (JPG, GIF, PNG, WebP, AVIF):
				<input type="file" name="myUpload[]" multiple accept="image/jpeg,image/png,image/gif,image/webp,image/avif"><br>
			</label><br>
            <label>
				Bitte geben Sie die Länge der längeren Bildseite in Pixel an (Standard: 800 px):
                <input type="number" name="neu_resolution" min="50" max="4000" value="800">
            </label>
			<input type="submit" name="HC" value="Hochladen und Konvertieren">
		</form>
        <br>
		<?php echo($msg); ?>
		<?php echo($msg3); ?>
        <?php if (!empty($msg2)): ?>
            <h2>Vorschau der konvertierten Bilder</h2>
		<?php echo $msg2; ?>
        <?php endif; ?>
	</body>
</html>