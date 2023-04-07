<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del POST
    $clasesJSON = $_POST['clasesJSON'];
    $clases = json_decode($clasesJSON, true);

    // Crear el array asociativo y agregar el array de clases dentro de él
    $clasesData = array("clases" => $clases);

    // Convertir el array asociativo a JSON con formato "pretty"
    $clasesJSON = json_encode($clasesData, JSON_PRETTY_PRINT);

    // Guardar el archivo JSON
    $guardado = file_put_contents("/app/src/Resources/clases.json", $clasesJSON);

    // Verificar si el archivo se guardó correctamente
    if ($guardado !== false) {
        var_dump("Archivo guardado correctamente");
    } else {
        var_dump("Error al guardar el archivo");
    }
}


