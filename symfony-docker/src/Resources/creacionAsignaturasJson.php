<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_start();

    // Obtener los datos del POST
    $asignaturasJSON = $_POST['asignaturasJSON'];

    // Decode del Json para luego aplicarle el JSON_PRETTY_PRINT
    $asignaturasDatos = json_decode($asignaturasJSON, true);

    // Convertir el array asociativo a JSON con formato "pretty"
    $asignaturasJSON = json_encode($asignaturasDatos, JSON_PRETTY_PRINT);

    // Guardar el archivo JSON
    $guardado = file_put_contents("/app/src/Resources/asignaturas.json", $asignaturasJSON);
    // Verificar si el archivo se guardó correctamente
    if ($guardado !== false) {
        var_dump("Archivo guardado correctamente");
    } else {
        var_dump("Error al guardar el archivo");
    }
}
