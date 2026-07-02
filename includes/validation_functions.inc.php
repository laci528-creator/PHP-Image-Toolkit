<?php

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


function validateResolution(mixed $resolution): array
 {
    $resolution = filter_var($resolution, FILTER_VALIDATE_INT);

    if ($resolution === false || $resolution < 50 || $resolution > 4000) {
        return [
            "success" => false,
            "message" => "Bitte geben Sie eine Auflösung zwischen 50 und 4000 Pixel an."
        ];
    }
    return [
        "success" => true,
        "resolution" => $resolution
    ];
}


?>