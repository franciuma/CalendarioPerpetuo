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
    private UsuarioGrupoRepository $usuarioGrupoRepository;
    private GrupoRepository $grupoRepository;
    private GrupoService $grupoService;
    private UsuarioGrupoService $usuarioGrupoService;

    public function __construct(
        TitulacionRepository $titulacionRepository,
        UsuarioService $usuarioService,
        GrupoRepository $grupoRepository,
        UsuarioRepository $usuarioRepository,
        GrupoService $grupoService,
        UsuarioGrupoService $usuarioGrupoService,
        UsuarioGrupoRepository $usuarioGrupoRepository
        ){
        $this->titulacionRepository = $titulacionRepository;
        $this->usuarioService = $usuarioService;
        $this->grupoRepository = $grupoRepository;
        $this->usuarioRepository = $usuarioRepository;
        $this->grupoService = $grupoService;
        $this->usuarioGrupoService = $usuarioGrupoService;
        $this->usuarioGrupoRepository = $usuarioGrupoRepository;
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

    #[Route('/seleccionar/alumno', name: 'app_seleccionar_alumno')]
    #[Route('/seleccionar/editar/alumno', name: 'app_seleccionar_editar_alumno')]
    public function mostrarCalendario(Request $request): Response
    {
        if($request->getPathInfo() == '/seleccionar/alumno') {
            $accion = "Ver calendario";
            $controlador = "app_seleccionar_alumno";
        } else {
            $accion = "Editar alumno";
            $controlador = "app_seleccionar_editar_alumno";
        }

        if ($request->isMethod('POST')) {
            $dni = $request->get("dniAlum");

            if($accion == "Ver calendario") {
                return $this->redirectToRoute('app_calendario_alumno',["dni" => $dni]);
            } else {
                return $this->redirectToRoute('app_editar_alumno',["dni" => $dni]);
            }
        }

        return $this->render('leer/alumno.html.twig', [
            'accion' => $accion,
            'controlador' => $controlador
        ]);
    }

    /**
     * Editar un alumno
     */
    #[Route('/editar/alumno', name: 'app_editar_alumno')]
    public function editarAlumno(Request $request): Response
    {
        $dni = $request->get("dni");
        $alumno = $this->usuarioRepository->findOneByDni($dni);
        $alumnoId = $alumno->getId();

        $alumnoGrupos = $this->usuarioGrupoRepository->findUsuarioGrupoByUsuarioId($alumno->getId());

        $gruposAlumnoArray = array_map(function($alumnoGrupo) {
            $titulacion = $alumnoGrupo->getGrupo()->getAsignatura()->getTitulacion();
            $grupo = $alumnoGrupo->getGrupo();
            return [
                'id' => $alumnoGrupo->getId(),
                'idCentro' => $titulacion->getCentro()->getId(),
                'letra' => $grupo->getLetra()."-".$grupo->getAsignatura()->getNombre()."-".$grupo->getHorario()
            ];
        }, $alumnoGrupos);

        $titulacion = $alumnoGrupos[0]->getGrupo()->getAsignatura()->getTitulacion();
        $centro = $titulacion->getCentro();
        $provincia = $centro->getProvincia();
        $nombreCompleto = $titulacion->getNombreTitulacion()." - ".$centro->getNombre()." - ".$provincia;

        //Obtener todos los grupos
        $grupos = $this->grupoRepository->findByTitulacionId($titulacion->getId());

        //Mandamos los atributos que vamos a utilizar para grupo
        $gruposArray = array_map(function($grupo) {
            return [
                'id' => $grupo->getId(),
                'letra' => $grupo->getLetra()."-".$grupo->getAsignatura()->getNombre()."-".$grupo->getHorario()
            ];
        }, $grupos);
        //creamos un json de los grupos para pasar al javascript
        $gruposJson = json_encode($gruposArray);

        $gruposAlumnoJson = json_encode($gruposAlumnoArray);

        return $this->render('editar/alumno.html.twig', [
            "alumno" => $alumno,
            "alumnoid" => $alumnoId,
            "titulacion" => $nombreCompleto,
            "gruposAlumno" => $gruposAlumnoJson,
            "grupos" => $gruposJson
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

    #[Route('/post/alumno/editado', name: 'app_post_alumno_editado')]
    public function postEditado(Request $request): Response
    {
        $mensaje = "Usuario editado correctamente";
        $alumnoId = $request->get('alumno');
        $alumno = $this->usuarioRepository->findOneById($alumnoId);

        $grupos = $this->usuarioRepository->findGruposByUsuarioId($alumnoId);
        $gruposNuevos = $this->grupoService->editarGruposAlumnos($grupos);
        $this->usuarioGrupoService->getUsuarioGrupo($alumno, $gruposNuevos);

        return $this->redirectToRoute('app_menu_alumno',["mensaje" => $mensaje]);
    }
}
