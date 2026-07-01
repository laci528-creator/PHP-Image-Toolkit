<?php
require("includes/config.inc.php");
require("includes/common.inc.php");

//ta($_POST);
//ta($_FILES);

$msg = "";
$msg2 = "";
$msg3 = "";

function addWatermark(
    string $imagePath,
    string $watermarkPath,
    string $outputPath,
    string $position = "bottom-right",
    int $opacity = 50
): bool {

    $imageInfo = getimagesize($imagePath);
    $watermarkInfo = getimagesize($watermarkPath);

    if ($imageInfo === false || $watermarkInfo === false) {
        return false;
    }

    $image = imagecreatefromjpeg($imagePath);
    $watermark = imagecreatefrompng($watermarkPath);

    if (!$image || !$watermark) {
        return false;
    }

    $imageWidth = imagesx($image);
    $imageHeight = imagesy($image);

    $watermarkWidth = imagesx($watermark);
    $watermarkHeight = imagesy($watermark);

    $margin = 20;

    switch ($position) {
        case "bottom-left":
            $x = $margin;
            $y = $imageHeight - $watermarkHeight - $margin;
            break;

        case "top-right":
            $x = $imageWidth - $watermarkWidth - $margin;
            $y = $margin;
            break;

        case "top-left":
            $x = $margin;
            $y = $margin;
            break;

        case "center":
            $x = (int)(($imageWidth - $watermarkWidth) / 2);
            $y = (int)(($imageHeight - $watermarkHeight) / 2);
            break;

        case "bottom-right":
        default:
            $x = $imageWidth - $watermarkWidth - $margin;
            $y = $imageHeight - $watermarkHeight - $margin;
            break;
    }

    imagecopymerge(
        $image,
        $watermark,
        $x,
        $y,
        0,
        0,
        $watermarkWidth,
        $watermarkHeight,
        $opacity
    );

    $outputDir = dirname($outputPath);

    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    




    $ok = imagejpeg($image, $outputPath, 90);

    imagedestroy($image);
    imagedestroy($watermark);

    return $ok;
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

function validateOpacity(mixed $opacity): array
{
    $opacity = filter_var($opacity, FILTER_VALIDATE_INT);

    if ($opacity === false || $opacity < 1 || $opacity > 100) {
        return [
            "success" => false,
            "message" => "Bitte geben Sie eine Transparenz zwischen 1 und 100 ein."
        ];
    }

    return [
        "success" => true,
        "opacity" => $opacity
    ];
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
                            $fino = finfo_open(FILEINFO_MIME_TYPE);
                            $watermarkMimeType = finfo_file($fino, $watermarkTmpName);
                            finfo_close($fino); 
                                if ($watermarkMimeType === "image/png") {
                                for ($i = 0; $i < $fileCount; $i++) {

                                    $file = getUploadedFileByIndex($f, $i);
                                    $filename = $file["name"];
                                        $finfo = finfo_open(FILEINFO_MIME_TYPE);
                                        $mimeType = finfo_file($finfo, $file["tmp_name"]);
                                        finfo_close($finfo);
                                    if ($mimeType === "image/jpeg") {
                                         $newFilename = createSafeFilename($filename, "jpeg");

                                         $outputPath = "output_image/" . $newFilename;
                                         $inputPath = $file["tmp_name"];

                                                $ok = addWatermark(
                                                    $inputPath,
                                                    $watermarkTmpName,
                                                    $outputPath,
                                                    $_POST["position"] ?? "bottom-right",
                                                    $o
                                                );
                                                    if ($ok) {
                                                        $convertedFiles[] = './output_image/' . $newFilename;
                                                        $msg .= '<p class="success">Wasserzeichen erfolgreich zu ' . htmlspecialchars($filename) . ' hinzugefügt.</p>';
                                                        $msg2 .= '<h3>Preview Image - ' . htmlspecialchars($newFilename) . '</h3><img src="./output_image/'. htmlspecialchars($newFilename) .'" alt="Konvertiertes Bild" style="max-width: 1000px; margin: 10px;">';
                                                    } else {
                                                        $msg2 .= '<p class="error">Fehler beim Hinzufügen des Wasserzeichens zu ' . htmlspecialchars($filename) . '.</p>';
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
		<title>Bildverarbeitung</title>
		<meta charset="utf-8">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/water.css@2/out/dark.css">
        <style>
            .download-button {
                display: inline-block;
                padding: 10px 20px;
                margin: 10px 0;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 5px;
            }
            .download-button:hover {
                background-color: #45a049;
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
<h1>Wasserzeichen zu Bildern hinzufügen</h1>
		
<form method="post" enctype="multipart/form-data">
    <label>
        Bilder auswählen:
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
		<?php
            echo($msg3);
            echo($msg);
			echo($msg2);
			
		?>
	</body>
</html>