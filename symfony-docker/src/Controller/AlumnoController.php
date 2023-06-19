<?php

namespace App\Controller;

use App\Repository\GrupoRepository;
use App\Repository\UsuarioRepository;
use App\Service\GrupoService;
use App\Service\UsuarioGrupoService;
use App\Service\UsuarioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlumnoController extends AbstractController
{
    private UsuarioService $usuarioService;
    private UsuarioRepository $usuarioRepository;
    private GrupoRepository $grupoRepository;
    private GrupoService $grupoService;
    private UsuarioGrupoService $usuarioGrupoService;

    public function __construct(
        UsuarioService $usuarioService,
        GrupoRepository $grupoRepository,
        UsuarioRepository $usuarioRepository,
        GrupoService $grupoService,
        UsuarioGrupoService $usuarioGrupoService
        ){
        $this->usuarioService = $usuarioService;
        $this->grupoRepository = $grupoRepository;
        $this->usuarioRepository = $usuarioRepository;
        $this->grupoService = $grupoService;
        $this->usuarioGrupoService = $usuarioGrupoService;
    }

    #[Route('/formulario/alumno', name: 'app_formulario_alumno')]
    public function index(): Response
    {
        //Obtenemos todos los alumnos
        $alumnos = $this->usuarioRepository->findAllAlumnos();
        //Mandamos los atributos que vamos a utilizar para alumno
        $alumnosArray = array_map(function($alumno) {
            return [
                'id' => $alumno->getId(),
                'dni' => $alumno->getDni()
            ];
        }, $alumnos);

        $alumnosJson = json_encode($alumnosArray);

        //Obtener todos los grupos
        $grupos = $this->grupoRepository->findAll();

        //Mandamos los atributos que vamos a utilizar para grupo
        $gruposArray = array_map(function($grupo) {
            return [
                'id' => $grupo->getId(),
                'letra' => $grupo->getLetra()."-".$grupo->getAsignatura()->getNombre()."-".$grupo->getHorario()
            ];
        }, $grupos);
        //creamos un json de los grupos para pasar al javascript
        $gruposJson = json_encode($gruposArray);

        return $this->render('formularios/alumno.html.twig', [
            'grupos' => $gruposJson,
            'alumnos' => $alumnosJson,
        ]);
    }

    #[Route('/post/alumno', name: 'app_post_alumno')]
    public function post(): Response
    {
        $mensaje = "Usuario añadido correctamente";
        $alumno = $this->usuarioService->getUsuario();
        //Buscar grupos
        $grupos = $this->grupoService->buscarGruposJson();
        //Añadir a usuario-grupo
        $this->usuarioGrupoService->getUsuarioGrupo($alumno, $grupos);
        
        return $this->redirectToRoute('app_menu_alumno',["mensaje" => $mensaje]);
    }
}
