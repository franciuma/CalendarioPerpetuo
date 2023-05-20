<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Clase encargada de manejar todos los POST de los formularios, creando un fichero JSON de cada uno de ellos.
 */
class ManejarPostsController extends AbstractController
{
    #[Route('/manejar/posts/docente', name: 'profesorGrupo')]
    #[Route('/manejar/posts/asignatura', name: 'asignaturas')]
    #[Route('/manejar/posts/clase', name: 'clases')]
    #[Route('/manejar/posts/centro', name: 'centro')]
    #[Route('/manejar/posts/festivoscentro', name: 'festivoscentro')]
    public function index(Request $request)
    {
        $entidad = $request->attributes->get('_route');

        if ($request->isMethod('POST')) {

            // Obtener los datos del POST
            $datosJSON = $request->get($entidad.'JSON');

            // Decode del Json para luego aplicarle el JSON_PRETTY_PRINT
            $datosDecode = json_decode($datosJSON, true);

            if($entidad == "asignaturas" || $entidad == "clases"){
                // Crear el array asociativo y agregar el array de asignaturas dentro de él
                $datosDecode = array($entidad => $datosDecode);
            }

            //Si es festivo se añade al JSON ya existente
            if(strpos($entidad, "festivo") !== false) {
                $centro = "festivosCentro".$request->get('nombreCentro');
                $datosDecode = self::aniadirFestivoJSON($entidad, $datosDecode, $centro);
            }

            // Convertir el array asociativo a JSON con formato "pretty"
            $datosJSONpretty = json_encode($datosDecode, JSON_PRETTY_PRINT);

            //Guardar el archivo JSON
            $guardado = file_put_contents("/app/src/Resources/".$entidad.".json", $datosJSONpretty);

            // Verificar si el archivo se guardó correctamente
            if ($guardado !== false) {
                var_dump("Archivo guardado correctamente");
            } else {
                var_dump("Error al guardar el archivo");
            }
        }
    }

    /**
     * Acopla los festivos nuevos al json de festivos en el nodo correspondiente.
     */
    public function aniadirFestivoJSON($entidad, $datosDecodeFestivo, $centro): array
    {
        //Obtenemos el array de festivos
        $datosJSONfestivo = file_get_contents("/app/src/Resources/".$entidad.".json");
        //Lo pasamos a array
        $arrayFestivos = json_decode($datosJSONfestivo, true);
        //Cogemos el nodo y metemos los datos
        $arrayFestivos[$centro] = array_merge($arrayFestivos[$centro], $datosDecodeFestivo);
        return $arrayFestivos;
    }
}
