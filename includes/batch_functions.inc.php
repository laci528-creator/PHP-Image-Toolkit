<?php 

function deleteDirectory(string $ordner): void
{
    if (!is_dir($ordner)) {
        return;
    }

    $eintraege = scandir($ordner);

    if ($eintraege === false) {
        return;
    }

    foreach ($eintraege as $eintrag) {

        if ($eintrag === '.' || $eintrag === '..') {
            continue;
        }

        $vollerPfad = rtrim($ordner, "/\\")
            . DIRECTORY_SEPARATOR
            . $eintrag;

        if (is_link($vollerPfad) || is_file($vollerPfad)) {
            unlink($vollerPfad);
        } elseif (is_dir($vollerPfad)) {
            deleteDirectory($vollerPfad);
        }
    }

    rmdir($ordner);
}

function cleanupOldBatchDirectories(
    string $zielmapp,
    int $maxAgeSeconds = 3600
): void {

    $basefolder = trim($zielmapp, "/\\");

    $projektRoot = dirname(__DIR__);

    $baseDir = $projektRoot
        . DIRECTORY_SEPARATOR
        . $basefolder;

    if (!is_dir($baseDir)) {
        return;
    }

    $entries = scandir($baseDir);

    if ($entries === false) {
        return;
    }

    $now = time();

    foreach ($entries as $entry) {

        if ($entry === "." || $entry === "..") {
            continue;
        }

        // Csak az alkalmazás által létrehozott
        // 16 karakteres batch ID-ket vizsgáljuk
        if (!preg_match('/^[a-f0-9]{16}$/', $entry)) {
            continue;
        }

        $batchDir = $baseDir
            . DIRECTORY_SEPARATOR
            . $entry;

        if (!is_dir($batchDir) || is_link($batchDir)) {
            continue;
        }

        $modifiedTime = filemtime($batchDir);

        if ($modifiedTime === false) {
            continue;
        }

        if (($now - $modifiedTime) > $maxAgeSeconds) {
            deleteDirectory($batchDir);
        }
    }
}

function createBatchPath(string $zielmapp): array
{
    // lösche alte files
    cleanupOldBatchDirectories($zielmapp);

    $batchId = bin2hex(random_bytes(8));

    $basefolder = trim($zielmapp, "/\\");

    $projektRoot = dirname(__DIR__);

    $batchDir = $projektRoot
        . DIRECTORY_SEPARATOR
        . $basefolder
        . DIRECTORY_SEPARATOR
        . $batchId
        . DIRECTORY_SEPARATOR;

    $outputDir = $batchDir
        . "output"
        . DIRECTORY_SEPARATOR;

    $publicBatchDir = $basefolder . "/" . $batchId . "/";
    $publicOutputDir = $publicBatchDir . "output/";

    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    return [
    "batchId" => $batchId,
    "batchDir" => $batchDir,
    "outputDir" => $outputDir,
    "publicOutputDir" => $publicOutputDir,
    ];
}
?>