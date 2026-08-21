<?php
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

?>
