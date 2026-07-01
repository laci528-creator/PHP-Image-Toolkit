<?php
require("includes/config.inc.php");
require("includes/common.inc.php");
require("includes/Format_converter_function.inc.php");


$msg = "";
$msg2 = "";
$msg3 = "";
$convertedFiles = [];

function validateUploadedImage(array $file, int $maxFileSize = 8388608): array
{
    $allowedMimeTypes = [
        "image/jpeg",
        "image/png",
        "image/gif",
        "image/webp",
        "image/avif"
    ];

    if (!isset($file["error"]) || $file["error"] !== UPLOAD_ERR_OK) {
        return [
            "success" => false,
            "message" => "Beim Hochladen der Datei ist ein Fehler aufgetreten."
        ];
    }

    if ($file["size"] > $maxFileSize) {
        return [
            "success" => false,
            "message" => "Die Datei ist zu groß."
        ];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file["tmp_name"]);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedMimeTypes, true)) {
        return [
            "success" => false,
            "message" => "Dieser Dateityp ist nicht erlaubt."
        ];
    }

    return [
        "success" => true,
        "message" => "Die Datei ist gültig.",
        "mime" => $mimeType
    ];
}

function validateQuality(mixed $quality): array
{
    $quality = filter_var($quality, FILTER_VALIDATE_INT);

    if ($quality === false || $quality < 1 || $quality > 99) {
        return [
            "success" => false,
            "message" => "Bitte geben Sie eine Qualität zwischen 1 und 99 ein."
        ];
    }

    return [
        "success" => true,
        "quality" => $quality
    ];
}

function validateOutputFormat(mixed $format): array
{
    $allowedFormats = ["jpeg", "png", "webp", "avif"];

    if (!is_string($format) || !in_array($format, $allowedFormats, true)) {
        return [
            "success" => false,
            "message" => "Bitte wählen Sie ein gültiges Ausgabeformat."
        ];
    }

    return [
        "success" => true,
        "format" => $format
    ];
}


function getUploadedFileByIndex(array $files, int $index): array
{
    return [
        "name" => $files["name"][$index],
        "type" => $files["type"][$index],
        "tmp_name" => $files["tmp_name"][$index],
        "error" => $files["error"][$index],
        "size" => $files["size"][$index]
    ];
}


function createSafeFilename(string $originalFilename, string $extension): string
{
    $nameOnly = pathinfo($originalFilename, PATHINFO_FILENAME);

    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nameOnly);

    return $safeName . '_' . bin2hex(random_bytes(8)) . "." . $extension;
}


function createZip(array $files, string $zipPath): bool
{
    if (!is_dir(dirname($zipPath))) {
        mkdir(dirname($zipPath), 0777, true);
    }

    $zip = new ZipArchive();

    if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        return false;
    }

    foreach ($files as $filePath) {
        if (file_exists($filePath)) {
            $zip->addFile($filePath, basename($filePath));
        }
    }

    return $zip->close();
}


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
        <style>
            .download-button {
                display: inline-block;
                padding: 0.6em 1em;
                background: #2d7dd2;
                color: white;
                text-decoration: none;
                border-radius: 6px;
            }
            .success {
                    margin:0.5em 0;
                    padding:0.2em;
                    border-left:10px solid green;
                    font-style:italic;
                }
            .error {
                    margin:0.5em 0;
                    padding:0.2em;
                    border-left:10px solid red;
                    font-weight:bold;
                    color:red;
                }
        </style>

	</head>
	<body>
		<h1>Bildformat Konverter</h1>
		
		<form method="post" enctype="multipart/form-data">
			<label>
				Bitte wählen Sie eine Bilddatei aus (jpeg, gif, png, webp, avif):
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
        <?php
            echo($msg3);
            echo($msg);
            echo($msg2);
        ?>
    </body>
</html>