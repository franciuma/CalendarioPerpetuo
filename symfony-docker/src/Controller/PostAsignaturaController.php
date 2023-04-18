<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function index(): Response
    {
        //Persistir las asignaturas del JSON a la bd
        $this->asignaturaService->getAsignaturas();

        return $this->render('posts/asignatura.html.twig', [
            'controller_name' => 'PostAsignaturaController',
        ]);
    }
}
