<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\ProfesorService;
use App\Service\GrupoService;

class PostProfesorController extends AbstractController
{
    private ProfesorService $profesorService;
    private GrupoService $grupoService;

    public function __construct(
        ProfesorService $profesorService,
        GrupoService $grupoService
    )
    {
        $this->profesorService = $profesorService;
        $this->grupoService = $grupoService;
    }

    #[Route('/post/docente', name: 'app_post_profesor')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Obtener los datos del POST
            $datosJSON = $_POST['jsonDatos'];

            // Decode del Json para luego aplicarle el JSON_PRETTY_PRINT
            $datos = json_decode($datosJSON, true);

            // Convertir el array asociativo a JSON con formato "pretty"
            $profesorgrupoJSONpretty = json_encode($datos, JSON_PRETTY_PRINT);

            // Guardar el archivo JSON
            $guardado = file_put_contents("/app/src/Resources/profesorGrupo.json", $profesorgrupoJSONpretty);
            // Verificar si el archivo se guardó correctamente
            if ($guardado !== false) {
                var_dump("Archivo guardado correctamente");
            } else {
                var_dump("Error al guardar el archivo");
            }
        }

        //Persistir el profesor del JSON a la bd
        $this->profesorService->getProfesor();
        //Persistir los grupos del JSON a la bd
        $this->grupoService->getGrupos();
        return $this->render('posts/profesor.html.twig', [
            'controller_name' => 'PostProfesorController',
        ]);
    }
}
