<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UsuarioService;
use App\Service\GrupoService;
use App\Service\UsuarioGrupoService;

class PostProfesorController extends AbstractController
{
    private UsuarioService $usuarioService;
    private GrupoService $grupoService;
    private UsuarioGrupoService $usuarioGrupoService;

    public function __construct(
        UsuarioService $usuarioService,
        GrupoService $grupoService,
        UsuarioGrupoService $usuarioGrupoService
    )
    {
        $this->usuarioService = $usuarioService;
        $this->grupoService = $grupoService;
        $this->usuarioGrupoService = $usuarioGrupoService;
    }

    #[Route('/post/docente', name: 'app_post_profesor')]
    public function index(): Response
    {
        //Persistir el profesor del JSON a la bd
        $profesor = $this->usuarioService->getProfesor();
        //Persistir los grupos del JSON a la bd y grupoProfesor
        $grupos = $this->grupoService->getGrupos();
        //Persistir en la tabla UsuarioGrupo
        $this->usuarioGrupoService->getUsuarioGrupo($profesor,$grupos);

        return $this->render('posts/profesor.html.twig', [
            'controller_name' => 'PostProfesorController',
        ]);
    }
}
