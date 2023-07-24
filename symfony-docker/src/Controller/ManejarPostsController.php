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
    #[Route('/manejar/posts/asignatura', name: 'asignaturas')]
    #[Route('/manejar/posts/clase', name: 'clases')]
    #[Route('/manejar/posts/centro', name: 'centro')]
    #[Route('/manejar/posts/festivoscentro', name: 'festivoscentro')]
    #[Route('/manejar/posts/festivosnacionales', name: 'festivosnacionales')]
    #[Route('/manejar/posts/festivoslocales', name: 'festivoslocales')]
    #[Route('/manejar/posts/titulaciones', name: 'titulaciones')]
    #[Route('/manejar/posts/usuarioGrupo', name: 'usuarioGrupo')]
    public function index(Request $request)
    {
        $entidad = $request->attributes->get('_route');

        if ($request->isMethod('POST')) {

            // Obtener los datos del POST
            $datosJSON = $request->get($entidad.'JSON');

            // Decode del Json para luego aplicarle el JSON_PRETTY_PRINT
            $datosDecode = json_decode($datosJSON, true);

            if($entidad == "asignaturas" || $entidad == "clases"){
                // Crear el array asociativo y agregar el array de la entidad dentro de él
                $datosDecode = array($entidad => $datosDecode);
            }

            //Si es festivo se añade al JSON ya existente, buscando el nodo donde colocarlo
            if(strpos($entidad, "festivo") !== false) {
                if($request->get('nombreCentro')) {
                    $nodo = "festivosCentro".$request->get('nombreCentro');
                } else if($request->get('provincia')){
                    $nodo = "festivosLocales".$request->get('provincia');
                } else {
                    // Si no es ninguno, es festivo nacional
                    $nodo = "festivosNacionales-España";
                }

                $datosDecode = self::aniadirFestivoJSON($entidad, $datosDecode, $nodo);
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
    public function aniadirFestivoJSON($entidad, $datosDecodeFestivo, $nodo): array
    {
        //Obtenemos el array de festivos
        $datosJSONfestivo = file_get_contents("/app/src/Resources/".$entidad.".json");
        //Lo pasamos a array
        $arrayFestivos = json_decode($datosJSONfestivo, true);
        //Cogemos el nodo y metemos los datos
        $arrayFestivos[$nodo] = array_merge($arrayFestivos[$nodo], $datosDecodeFestivo);
        return $arrayFestivos;
    }

    public function editarPosts($entidad, $festivoArray): void
    {
        // Se obtiene el JSON
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosNacionales.json');
        $festivosNacionalesArrayJson = json_decode($festivosJson, true);

        // Buscar el festivo a editar en el $festivosNacionalesArrayJson
        foreach ($festivosNacionalesArrayJson['festivosNacionales-España'] as &$festivo) {
            if ($festivo['nombre'] === $festivoArray[0]['nombre']) {
                // Actualizar el inicio y el final del festivo
                $festivo['inicio'] = $festivoArray[0]['inicio'];
                $festivo['final'] = $festivoArray[0]['final'];
                break;
            }
        }

        // Convertir el array actualizado a JSON
        $festivosNacionalesActualizadosJson = json_encode($festivosNacionalesArrayJson, JSON_PRETTY_PRINT);

        // Guardar el JSON actualizado en el archivo o donde sea que esté almacenado
        file_put_contents("/app/src/Resources/".$entidad.".json", $festivosNacionalesActualizadosJson);
    }
}
