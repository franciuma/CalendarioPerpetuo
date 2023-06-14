<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UsuarioService;
use App\Service\GrupoService;
use App\Service\UsuarioGrupoService;
use Symfony\Component\HttpFoundation\Request;

class PostProfesorController extends AbstractController
{
    private UsuarioRepository $usuarioRepository;
    private UsuarioService $usuarioService;
    private GrupoService $grupoService;
    private UsuarioGrupoService $usuarioGrupoService;

    public function __construct(
        UsuarioRepository $usuarioRepository,
        UsuarioService $usuarioService,
        GrupoService $grupoService,
        UsuarioGrupoService $usuarioGrupoService
    )
    {
        $this->usuarioRepository = $usuarioRepository;
        $this->usuarioService = $usuarioService;
        $this->grupoService = $grupoService;
        $this->usuarioGrupoService = $usuarioGrupoService;
    }

    /**
     * Muestra la plantilla de "Docente añadido correctamente".
     * Además, se persisten y editan los profesores, grupos y usuarios grupos correspondientes.
     * 
     */
    #[Route('/post/docente', name: 'app_post_profesor')]
    #[Route('/post/docente/editado', name: 'app_post_profesor_editado')]
    public function index(Request $request): Response
    {
        $accion = "agregado";
        if(($request->getPathInfo() == '/post/docente')) {
            //Persistir el profesor del JSON a la bd
            $profesor = $this->usuarioService->getProfesor();
            //Persistir los grupos del JSON a la bd y grupoProfesor
            $grupos = $this->grupoService->getGrupos(true);
            //Persistir en la tabla UsuarioGrupo
            $this->usuarioGrupoService->getUsuarioGrupo($profesor,$grupos);
        } else {
            //Si viene del editado
            $accion = "editado";
            $profesorId = $request->get('profesor');
            //Editamos el profesor
            $profesor = $this->usuarioRepository->findOneById($profesorId);
            $this->usuarioService->editarProfesor($profesor);
            //Editamos los grupos
            $grupos = $this->usuarioRepository->findGruposByUsuarioId($profesorId);
            $gruposNuevos = $this->grupoService->editarGrupos($grupos);
            //Obtenemos los grupos y el profesor actualizados
            $profesorUsuarioGrupo = $this->usuarioRepository->findOneById($profesorId);
            $this->usuarioGrupoService->getUsuarioGrupo($profesorUsuarioGrupo, $gruposNuevos);
        }

        return $this->render('posts/profesor.html.twig', [
            'accion' => $accion,
        ]);
    }
}
