<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\AsignaturaService;

class PostAsignaturaController extends AbstractController
{
    private AsignaturaService $asignaturaService;

    public function __construct(
        AsignaturaService $asignaturaService
    )
    {
        $this->asignaturaService = $asignaturaService;
    }

    #[Route('/post/asignatura', name: 'app_post_asignatura')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Obtener los datos del POST
            $asignaturasJSON = $_POST['asignaturasJSON'];

            // Decode del Json para luego aplicarle el JSON_PRETTY_PRINT
            $asignaturas = json_decode($asignaturasJSON, true);

            // Crear el array asociativo y agregar el array de asignaturas dentro de él
            $asignaturasDatos = array("asignaturas" => $asignaturas);

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

        //Persistir las asignaturas del JSON a la bd
        $this->asignaturaService->getAsignaturas();

        return $this->render('posts/asignatura.html.twig', [
            'controller_name' => 'PostAsignaturaController',
        ]);
    }
}
