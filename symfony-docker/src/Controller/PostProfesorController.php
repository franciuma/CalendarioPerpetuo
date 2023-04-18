<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ProfesorService;
use App\Service\GrupoService;
use App\Service\ProfesorGrupoService;

class PostProfesorController extends AbstractController
{
    private ProfesorService $profesorService;
    private GrupoService $grupoService;
    private ProfesorGrupoService $profesorGrupoService;

    public function __construct(
        ProfesorService $profesorService,
        GrupoService $grupoService,
        ProfesorGrupoService $profesorGrupoService
    )
    {
        $this->profesorService = $profesorService;
        $this->grupoService = $grupoService;
        $this->profesorGrupoService = $profesorGrupoService;
    }

    #[Route('/post/docente', name: 'app_post_profesor')]
    public function index(): Response
    {
        //Persistir el profesor del JSON a la bd
        $profesor = $this->profesorService->getProfesor();
        //Persistir los grupos del JSON a la bd y grupoProfesor
        $grupos = $this->grupoService->getGrupos();
        //Persistir en la tabla ProfesorGrupo
        $this->profesorGrupoService->getProfesorGrupo($profesor,$grupos);

        return $this->render('posts/profesor.html.twig', [
            'controller_name' => 'PostProfesorController',
        ]);
    }
}
