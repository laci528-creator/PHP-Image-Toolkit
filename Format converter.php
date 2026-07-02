<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/filename_functions.inc.php");
require("includes/image_functions.inc.php");
require("includes/upload_functions.inc.php");
require("includes/validation_functions.inc.php");
require("includes/zip_functions.inc.php");

$msg = "";
$msg2 = "";
$msg3 = "";

$maxFiles = 6;
$convertedFiles = [];

if($_SERVER["REQUEST_METHOD"] === "POST") {
    if(!empty($_FILES["myUpload"]["name"][0])) {
        $quality = ($_POST["quality"] ?? "") === "" ? 85 : $_POST["quality"];
        $valQuality = validateQuality($quality);

            if($valQuality['success'] === true) {
                $q = $valQuality["quality"];
                $formatValidation = validateOutputFormat($_POST["output_format"] ?? "");
                
                    if ($formatValidation["success"]) {
                        $outputformat = $formatValidation["format"];
                            $f = $_FILES["myUpload"];
                            $fileCount = count($f["name"]);
                            if($fileCount <= $maxFiles) {
                                for ($i = 0; $i < $fileCount; $i++) {
                                    $file = getUploadedFileByIndex($f, $i);
                                    $filename = $file["name"];
                                    $validation = validateUploadedImage($file);

                                        if($validation['success'])  {
                        
                                        $newFilename = createSafeFilename($filename, $outputformat);
                                        $inputPath = $file["tmp_name"];
                                        $ok = Format_konvert($newFilename,$inputPath,$outputformat,$q);

                                            if($ok) {
                                                $convertedFiles[] = './output_image/' . $newFilename;

                                                $msg .=  '<p class="success">Die Datei <strong>' . htmlspecialchars($newFilename) . '</strong> wurde erfolgreich konvertiert auf ' . htmlspecialchars($outputformat) .' format.</p>';
                                                $msg2 .= '<h3>Preview Image - ' . htmlspecialchars($newFilename) . '</h3><img src="./output_image/'. htmlspecialchars($newFilename) .'" alt="Konvertiertes Bild" style="max-width: 1000px; margin: 10px;">';
                                            } 
                                            else {
                                                    $msg .= '<p class="error">Die Konvertierung von ' . htmlspecialchars($filename) . ' ist fehlgeschlagen.</p>';
                                                }

                                        }
                                        else {
                                            $msg .= '<p class="error">' . htmlspecialchars($validation["message"]) . '</p>';
                                            }
                                }
                            }
                            else {
                                $msg = '<p class="error">Bitte laden Sie maximal ' . $maxFiles . ' Bilder hoch.</p>';
                                }
                    }
                    else {
                            $msg .= '<p class="error">' . htmlspecialchars($formatValidation["message"]) . '</p>';
                        }
            }
            else {
                    $msg .= $valQuality['message'];
                }
    } else {
        $msg = '<p class="error">Bitte wählen Sie mindestens eine Bilddatei aus.</p>';
        }

}


if (!empty($convertedFiles)) {
    $zipName = 'converted_images_' . bin2hex(random_bytes(6)) . '.zip';
    $zipPath = './zip/' . $zipName;

    $zipCreated = createZip($convertedFiles, $zipPath);

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
		<title>Bildformat konverter</title>
		<meta charset="utf-8">

		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
        <link rel="stylesheet" href="css/common.css">
	</head>
	<body>
        <?php require("includes/header.inc.php"); ?>
		<h1>Bildformat Konverter</h1>
		
		<form method="post" enctype="multipart/form-data">
			<label>
				Bitte wählen Sie eine Bilddatei aus (jpeg, gif, png, webp, avif)[Maximal 6 Dateien]:
				<input type="file" name="myUpload[]" multiple accept="image/jpeg,image/png,image/gif,image/webp,image/avif"><br>
			</label><br>
            <label>
				Bitte geben Sie die gewünschte Bildformat (Erlaubte Bildformat - jpeg, gif, png, webp, avif ):
                <select name="output_format">
                    <option value="jpeg">JPEG</option>
                    <option value="png">PNG</option>
                    <option value="webp">WebP</option>
                    <option value="avif">AVIF</option>
                </select>
            </label>
            <label>
				Bitte geben Sie die gewünschte Bildqualität ein (1-99) [Default bildquality: 85]:
                <input type="number" name="quality" min="1" max="99" value="85">
            </label>
			<input type="submit" value="Hochladen und Konvert">
		</form>
        <?php echo($msg3); ?>
        <?php echo($msg); ?>
        <?php if (!empty($msg2)): ?>
            <h2>Vorschau der konvertierten Bilder</h2>
		<?php echo $msg2; ?>
        <?php endif; ?>
    </body>
</html>