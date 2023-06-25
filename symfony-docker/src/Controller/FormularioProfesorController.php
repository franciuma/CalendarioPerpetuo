<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AsignaturaRepository;
use App\Repository\GrupoRepository;
use App\Repository\UsuarioGrupoRepository;
use App\Repository\UsuarioRepository;
use App\Service\UsuarioService;
use Symfony\Component\HttpFoundation\Request;

class FormularioProfesorController extends AbstractController
{
    
    private AsignaturaRepository $asignaturaRepository;
    private UsuarioService $usuarioService;
    private UsuarioRepository $usuarioRepository;
    private GrupoRepository $grupoRepository;
    private UsuarioGrupoRepository $usuarioGrupoRepository;

    public function __construct(
        AsignaturaRepository $asignaturaRepository,
        UsuarioService $usuarioService,
        UsuarioRepository $usuarioRepository,
        GrupoRepository $grupoRepository,
        UsuarioGrupoRepository $usuarioGrupoRepository
        ){
        $this->asignaturaRepository = $asignaturaRepository;
        $this->usuarioService = $usuarioService;
        $this->usuarioRepository = $usuarioRepository;
        $this->grupoRepository = $grupoRepository;
        $this->usuarioGrupoRepository = $usuarioGrupoRepository;
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
            'controller_name' => 'FormularioProfesorController',
            'asignaturas' => $asignaturasArray,
            'nombreProfesores' => $profesoresJson
        ]);
    }

    #[Route('/eliminar/docente', name: 'app_eliminar_profesor')]
    public function eliminarProfesor(Request $request): Response
    {
        $profesores = "";
        if ($request->isMethod('POST')) {
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
        }

        $conCalendario = false;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        return $this->render('eliminar/profesor.html.twig', [
            'profesores' => $profesores
        ]);
    }

    #[Route('/editar/docente', name: 'app_editar_profesor')]
    public function editarProfesor(Request $request): Response
    {
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
            'profesorid' => $profesorId
        ]);
    }

    #[Route('/seleccionar/docente', name: 'app_seleccionar_profesor')]
    #[Route('/lista/docente', name: 'app_lista_profesor')]
    public function seleccionarProfesor(Request $request): Response
    {
        $rutaActual = $request->getPathInfo();

        $conCalendario = false;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        if($rutaActual == '/seleccionar/docente') {
            return $this->render('leer/profesor.html.twig', [
                'profesores' => $profesores
            ]);
        } else {
            return $this->render('listar/profesor.html.twig', [
                'profesores' => $profesores
            ]);
        }
    }
}
