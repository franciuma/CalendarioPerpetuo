<?php

namespace App\Controller;

use App\Repository\GrupoRepository;
use App\Repository\TitulacionRepository;
use App\Repository\UsuarioGrupoRepository;
use App\Repository\UsuarioRepository;
use App\Service\GrupoService;
use App\Service\UsuarioGrupoService;
use App\Service\UsuarioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AlumnoController extends AbstractController
{
    private $titulacionSeleccionada = "";
    private TitulacionRepository $titulacionRepository;
    private UsuarioService $usuarioService;
    private UsuarioRepository $usuarioRepository;
    private GrupoRepository $grupoRepository;
    private GrupoService $grupoService;
    private UsuarioGrupoRepository $usuarioGrupoRepository;
    private UsuarioGrupoService $usuarioGrupoService;

    public function __construct(
        TitulacionRepository $titulacionRepository,
        UsuarioService $usuarioService,
        GrupoRepository $grupoRepository,
        UsuarioRepository $usuarioRepository,
        UsuarioGrupoRepository $usuarioGrupoRepository,
        GrupoService $grupoService,
        UsuarioGrupoService $usuarioGrupoService
        ){
        $this->titulacionRepository = $titulacionRepository;
        $this->usuarioService = $usuarioService;
        $this->grupoRepository = $grupoRepository;
        $this->usuarioRepository = $usuarioRepository;
        $this->usuarioGrupoRepository = $usuarioGrupoRepository;
        $this->grupoService = $grupoService;
        $this->usuarioGrupoService = $usuarioGrupoService;
    }

    #[Route('/formulario/alumno', name: 'app_formulario_alumno')]
    public function index(Request $request): Response
    {
        $disponible = "disabled";

        //Obtener las titulaciones
        $titulaciones = $this->titulacionRepository->findAll();

        $titulacionesArray = array_map(function($titulacion) {
            return [
                'id' => $titulacion->getId(),
                'idCentro' => $titulacion->getCentro()->getId(),
                'nombre' => $titulacion->getNombreTitulacion()." - ".$titulacion->getCentro()->getNombre()." - ".$titulacion->getCentro()->getProvincia()
            ];
        }, $titulaciones);

        $gruposJson = json_encode("");
        if ($request->isMethod('POST')) {
            $disponible = "enabled";
            $titulacion = explode("/",$request->request->get('titulAlum'));
            $titulacionId = $titulacion[0];
            $this->titulacionSeleccionada = $titulacion[1];
            //Obtener todos los grupos
            $grupos = $this->grupoRepository->findByTitulacionId($titulacionId);

            //Mandamos los atributos que vamos a utilizar para grupo
            $gruposArray = array_map(function($grupo) {
                return [
                    'id' => $grupo->getId(),
                    'letra' => $grupo->getLetra()."-".$grupo->getAsignatura()->getNombre()."-".$grupo->getHorario()
                ];
            }, $grupos);
            //creamos un json de los grupos para pasar al javascript
            $gruposJson = json_encode($gruposArray);
        }

        $alumnos = $this->usuarioRepository->findAllAlumnos();
        //Mandamos los atributos que vamos a utilizar para alumno
        $alumnosArray = array_map(function($alumno) {
            return [
                'id' => $alumno->getId(),
                'dni' => $alumno->getDni()
            ];
        }, $alumnos);

        $alumnosJson = json_encode($alumnosArray);

        return $this->render('formularios/alumno.html.twig', [
            'grupos' => $gruposJson,
            'alumnos' => $alumnosJson,
            'titulaciones' => $titulacionesArray,
            'disponible' => $disponible,
            'titulacionSeleccionada' => $this->titulacionSeleccionada
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

    #[Route('/seleccionar/alumno', name: 'app_seleccionar_alumno')]
    public function mostrarCalendario(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $dni = $request->get("dniAlum");
            return $this->redirectToRoute('app_calendario_alumno',["dni" => $dni]);
        }

        return $this->render('leer/alumno.html.twig', []);
    }
}
