<?php 
function createBatchPath(string $zielmapp): array {
    $batchId = bin2hex(random_bytes(8));

    $basefolder = trim($zielmapp, '/');

    $projektRoot = dirname(__DIR__);
    $batchDir = $projektRoot . '/' . $basefolder . '/' . $batchId . '/';
    $outputDir = $batchDir . 'output/';

    $publicBatchDir = $basefolder . '/' . $batchId . '/';
    $publicOutputDir = $publicBatchDir . 'output/';

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