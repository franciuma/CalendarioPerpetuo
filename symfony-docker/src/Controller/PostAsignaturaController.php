<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AsignaturaService;
use App\Service\LeccionService;

class PostAsignaturaController extends AbstractController
{
    private AsignaturaService $asignaturaService;
    private LeccionService $leccionService;

    public function __construct(
        AsignaturaService $asignaturaService,
        LeccionService $leccionService
    )
    {
        $this->asignaturaService = $asignaturaService;
        $this->leccionService = $leccionService;
    }

    #[Route('/post/asignatura', name: 'app_post_asignatura')]
    public function index(): Response
    {
        //Persistir las asignaturas del JSON a la bd
        $this->asignaturaService->getAsignaturas();
        //Persistir las clases del JSON a la bd
        $this->leccionService->getLecciones();

        return $this->render('posts/asignatura.html.twig', [
            'controller_name' => 'PostAsignaturaController',
        ]);
    }
}
