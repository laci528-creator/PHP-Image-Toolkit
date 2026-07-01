<?php


function createSafeFilename(string $originalFilename, string $extension): string
{
    $nameOnly = pathinfo($originalFilename, PATHINFO_FILENAME);

    $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $nameOnly);

    return $safeName . '_' . bin2hex(random_bytes(8)) . "." . $extension;
}

?>
