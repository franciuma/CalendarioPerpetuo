<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AsignaturaService;
use App\Service\LeccionService;
use App\Service\TitulacionService;

class PostAsignaturaController extends AbstractController
{
    private AsignaturaService $asignaturaService;
    private LeccionService $leccionService;
    private TitulacionService $titulacionService;

    public function __construct(
        AsignaturaService $asignaturaService,
        LeccionService $leccionService,
        TitulacionService $titulacionService
    )
    {
        $this->asignaturaService = $asignaturaService;
        $this->leccionService = $leccionService;
        $this->titulacionService = $titulacionService;
    }

    #[Route('/post/asignatura', name: 'app_post_asignatura')]
    public function index(): Response
    {
        //Persistir las titulaciones y devolver array de objetos Titulacion
        $this->titulacionService->getTitulaciones();
        //Persistir las asignaturas del JSON a la bd
        $this->asignaturaService->getAsignaturas();
        //Persistir las clases del JSON a la bd
        $this->leccionService->getLecciones();

        return $this->render('posts/asignatura.html.twig', [
            'controller_name' => 'PostAsignaturaController',
        ]);
    }
}
