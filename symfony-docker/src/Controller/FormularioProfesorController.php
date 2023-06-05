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

        $titulosAsignaturas = array_map(function ($asignatura) {
            return $asignatura->getNombre();
        }, $asignaturas);

        return $this->render('formularios/profesor.html.twig', [
            'controller_name' => 'FormularioProfesorController',
            'asignaturas' => $titulosAsignaturas
        ]);
    }

    #[Route('/formulario/eliminar/docente', name: 'app_eliminar_profesor')]
    public function eliminarProfesor(Request $request): Response
    {
        $profesores = "";
        if ($request->isMethod('POST')) {
            $profesor = $request->request->get('profesorSeleccionado');
            $nombreCompleto = explode(" ", $profesor);
            //Asignamos el nombre y apellidos
            $nombre = $nombreCompleto[0];
            $apellidoPr = $nombreCompleto[1];
            $apellidoSeg = $nombreCompleto[2];
            //Obtenemos el profesor, su usuarioGrupo y sus grupos
            $profesorObjeto = $this->usuarioRepository->findOneByNombreApellidos($nombre, $apellidoPr, $apellidoSeg);
            $grupos = $this->usuarioRepository->findGruposByUsuario($nombre, $apellidoPr, $apellidoSeg);
            $profesorGrupo = $this->usuarioGrupoRepository->findUsuarioGrupoByUsuarioId($profesorObjeto->getId());

            $this->usuarioGrupoRepository->removeUsuarioGrupos($profesorGrupo, true);
            $this->grupoRepository->removeGrupos($grupos, true);
            $this->usuarioRepository->remove($profesorObjeto, true);
        }

        $conCalendario = false;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        return $this->render('eliminar/profesor.html.twig', [
            'profesores' => $profesores
        ]);
    }
}
