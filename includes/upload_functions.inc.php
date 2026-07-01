<?php 

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



?>