<?php
require_once __DIR__ . "/includes/config.inc.php";
require_once __DIR__ . "/includes/batch_functions.inc.php";


if (!isset($_GET['batch']) || $_GET['batch'] === '') {
    exit('Keine Batch-ID angegeben.');
}

$batchId = basename($_GET['batch']);

if (!preg_match('/^[a-f0-9]{16}$/', $batchId)) {
    exit('Ungültige Batch-ID.');
}

$batchPfad = __DIR__ . '/uploads_bildconverter/' . $batchId . '/';
$datei = $batchId . '.zip';
$pfad = $batchPfad . $datei;

if (!file_exists($pfad)) {
    exit('Datei nicht gefunden.');
}

if (pathinfo($pfad, PATHINFO_EXTENSION) !== 'zip') {
    exit('Ungültige Datei.');
}

header('Content-Description: File Transfer');
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $datei . '"');
header('Content-Length: ' . filesize($pfad));
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$ok = readfile($pfad);

if ($ok !== false) {
    deleteDirectory($batchPfad);
}

exit;