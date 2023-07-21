<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AsignaturaRepository;
use App\Repository\GrupoRepository;
use App\Repository\UsuarioGrupoRepository;
use App\Repository\UsuarioRepository;
use App\Service\GrupoService;
use App\Service\UsuarioGrupoService;
use App\Service\UsuarioService;
use Symfony\Component\HttpFoundation\Request;

class ProfesorController extends AbstractController
{
    
    private AsignaturaRepository $asignaturaRepository;
    private UsuarioService $usuarioService;
    private UsuarioRepository $usuarioRepository;
    private GrupoRepository $grupoRepository;
    private UsuarioGrupoRepository $usuarioGrupoRepository;
    private GrupoService $grupoService;
    private UsuarioGrupoService $usuarioGrupoService;

    public function __construct(
        AsignaturaRepository $asignaturaRepository,
        UsuarioService $usuarioService,
        UsuarioRepository $usuarioRepository,
        GrupoRepository $grupoRepository,
        UsuarioGrupoRepository $usuarioGrupoRepository,
        GrupoService $grupoService,
        UsuarioGrupoService $usuarioGrupoService
        ){
        $this->asignaturaRepository = $asignaturaRepository;
        $this->usuarioService = $usuarioService;
        $this->usuarioRepository = $usuarioRepository;
        $this->grupoRepository = $grupoRepository;
        $this->usuarioGrupoRepository = $usuarioGrupoRepository;
        $this->grupoService = $grupoService;
        $this->usuarioGrupoService = $usuarioGrupoService;
    }

    #[Route('/formulario/docente', name: 'app_formulario_profesor')]
    public function index(): Response
    {
        $asignaturas = $this->asignaturaRepository->findAll();

        $asignaturasArray = array_map(function ($asignatura) {
            return [
                'asignatura' => $asignatura->getNombre(),
                'centro' => $asignatura->getTitulacion()->getCentro()->getNombre()
            ];
        }, $asignaturas);

        $profesores = $this->usuarioRepository->findAllProfesores();

        $profesoresArray = array_map(function($profesor) {
            return [
                'nombreCompleto' => $profesor->getNombre()." ".$profesor->getPrimerApellido()." ".$profesor->getSegundoApellido()
            ];
        }, $profesores);
        
        $profesoresJson = json_encode($profesoresArray);

        return $this->render('formularios/profesor.html.twig', [
            'asignaturas' => $asignaturasArray,
            'nombreProfesores' => $profesoresJson
        ]);
    }

    #[Route('/eliminar/docente', name: 'app_eliminar_profesor')]
    public function eliminarProfesor(Request $request): Response
    {
        $profesores = "";
        if ($request->isMethod('POST')) {
            $mensaje = "Docente eliminado correctamente";
            $profesor = $request->request->get('profesorSeleccionado');
            $nombreCompleto = explode(" ", $profesor);
            //Asignamos el nombre y apellidos
            $apellidoPr = $nombreCompleto[count($nombreCompleto) - 2];
            $apellidoSeg = $nombreCompleto[count($nombreCompleto) - 1];
            $nombre = implode(" ", array_slice($nombreCompleto, 0, count($nombreCompleto) - 2));

            //Obtenemos el profesor, su usuarioGrupo y sus grupos
            $profesorObjeto = $this->usuarioRepository->findOneByNombreApellidos($nombre, $apellidoPr, $apellidoSeg);
            $grupos = $this->usuarioRepository->findGruposByUsuario($nombre, $apellidoPr, $apellidoSeg);
            $profesorGrupo = $this->usuarioGrupoRepository->findUsuarioGrupoByUsuarioId($profesorObjeto->getId());

            $this->usuarioGrupoRepository->removeUsuarioGrupos($profesorGrupo);
            $this->grupoRepository->removeGrupos($grupos);
            $this->usuarioRepository->remove($profesorObjeto);
            $this->usuarioRepository->flush();

            return $this->redirectToRoute('app_menu_docentes_admin', ["mensaje" => $mensaje]);
        }

        $conCalendario = false;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        return $this->render('eliminar/profesor.html.twig', [
            'profesores' => $profesores
        ]);
    }

    #[Route('/editar/docente', name: 'app_editar_profesor')]
    #[Route('/editar/docente/admin', name: 'app_editar_profesor_admin')]
    public function editarProfesor(Request $request): Response
    {
        $url = $request->getPathInfo();
        $usuario = "Docente";

        if($url == "/editar/docente/admin") {
            $usuario = "Administrador";
        }

        $asignaturas = $this->asignaturaRepository->findAll();
        $profesor = $request->request->get('profesorSeleccionado');
        $nombreCompleto = explode(" ", $profesor);
        //Asignamos el nombre y apellidos
        $apellidoPr = $nombreCompleto[count($nombreCompleto) - 2];
        $apellidoSeg = $nombreCompleto[count($nombreCompleto) - 1];
        $nombre = implode(" ", array_slice($nombreCompleto, 0, count($nombreCompleto) - 2));

        //Obtenemos el profesor y sus grupos
        $profesorObjeto = $this->usuarioRepository->findOneByNombreApellidos($nombre, $apellidoPr, $apellidoSeg);
        $profesorId = $profesorObjeto->getId();
        $grupos = $this->usuarioRepository->findGruposByUsuario($nombre, $apellidoPr, $apellidoSeg);

        //Mandamos los atributos que vamos a utilizar para los grupos
        $gruposArray = array_map(function($grupo) {
            return [
                'id' => $grupo->getId(),
                'letra' => $grupo->getLetra(),
                'asignatura' => $grupo->getAsignatura()->getNombre(),
                'diasTeoria' => $grupo->getDiasTeoria(),
                'diasPractica' => $grupo->getDiasPractica(),
                'horario' => $grupo->getHorario(),
            ];
        }, $grupos);
        
        //creamos un json de los grupos para pasar al javascript
        $gruposJson = json_encode($gruposArray);

        $asignaturasArray = array_map(function ($asignatura) {
            return [
                'asignatura' => $asignatura->getNombre(),
                'centro' => $asignatura->getTitulacion()->getCentro()->getNombre()
            ];
        }, $asignaturas);

        return $this->render('editar/profesor.html.twig', [
            'asignaturas' => $asignaturasArray,
            'profesor' => $profesorObjeto,
            'grupos' => $gruposJson,
            'profesorid' => $profesorId,
            'usuario' => $usuario
        ]);
    }

    #[Route('/seleccionar/docente', name: 'app_seleccionar_profesor')]
    #[Route('/seleccionar/docente/admin', name: 'app_seleccionar_profesor_admin')]
    public function seleccionarProfesor(Request $request): Response
    {
        $url = $request->getPathInfo();
        $usuario = "Administrador";
        $controlador = 'app_editar_profesor_admin';

        $conCalendario = false;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        if($url == '/seleccionar/docente') {
            $usuario = "Docente";
            $controlador = 'app_editar_profesor';
        }

        return $this->render('leer/profesor.html.twig', [
            'profesores' => $profesores,
            'usuario' => $usuario,
            'controlador' => $controlador
        ]);
    }

    #[Route('/post/docente', name: 'app_post_profesor')]
    #[Route('/post/docente/editado', name: 'app_post_profesor_editado')]
    #[Route('/post/docente/admin', name: 'app_post_profesor_admin')]
    #[Route('/post/docente/editado/admin', name: 'app_post_profesor_editado_admin')]
    public function post(Request $request): Response
    {
        $url = $request->getPathInfo();
        $mensaje = "Docente agregado correctamente";
        if(($request->getPathInfo() == '/post/docente')) {
            //Persistir el profesor del JSON a la bd
            $profesor = $this->usuarioService->getUsuario();
            //Persistir los grupos del JSON a la bd y grupoProfesor
            $grupos = $this->grupoService->getGrupos(true);
            //Persistir en la tabla UsuarioGrupo
            $this->usuarioGrupoService->getUsuarioGrupo($profesor,$grupos);
        } else {
            //Si viene del editado
            $mensaje = "Docente editado correctamente";
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

        if($url == '/post/docente/editado') {
            return $this->redirectToRoute('app_menu_calendario_docente', ["mensaje" => $mensaje]);
        } else {
            return $this->redirectToRoute('app_menu_docentes_admin', ["mensaje" => $mensaje]);
        }
    }
}
