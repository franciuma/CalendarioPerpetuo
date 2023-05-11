<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AsignaturaRepository;
use App\Repository\FestivoCentroRepository;
use App\Repository\FestivoLocalRepository;
use App\Repository\FestivoNacionalRepository;
use App\Repository\LeccionRepository;
use App\Service\CalendarioService;
use App\Repository\UsuarioRepository;

class FormularioCalendarioController extends AbstractController
{

    private AsignaturaRepository $asignaturaRepository;
    private LeccionRepository $leccionRepository;
    private FestivoLocalRepository $festivoLocalRepository;
    private FestivoNacionalRepository $festivoNacionalRepository;
    private FestivoCentroRepository $festivoCentroRepository;
    private CalendarioService $calendarioService;
    private UsuarioRepository $usuarioRepository;

    public function __construct(
        AsignaturaRepository $asignaturaRepository,
        LeccionRepository $leccionRepository,
        FestivoLocalRepository $festivoLocalRepository,
        FestivoNacionalRepository $festivoNacionalRepository,
        FestivoCentroRepository $festivoCentroRepository,
        CalendarioService $calendarioService,
        UsuarioRepository $usuarioRepository
        ){
        $this->asignaturaRepository = $asignaturaRepository;
        $this->leccionRepository = $leccionRepository;
        $this->festivoLocalRepository = $festivoLocalRepository;
        $this->festivoNacionalRepository = $festivoNacionalRepository;
        $this->festivoCentroRepository = $festivoCentroRepository;
        $this->calendarioService = $calendarioService;
        $this->usuarioRepository = $usuarioRepository;
    }

    #[Route('/formulario/calendario', name: 'app_formulario_calendario')]
    public function index(): Response
    {
        //Obtener las asignaturas por nombre totales:
        $asignaturas = $this->asignaturaRepository->findAll();

        $titulosAsignaturas = array_map(function ($asignatura) {
            return $asignatura->getNombre();
        }, $asignaturas);

        $centroJson = file_get_contents(__DIR__ . '/../resources/centro.json');
        $centroArray = json_decode($centroJson, true);

        //Obtenemos profesor introducido en la base de datos
        $profesor = $this->calendarioService->getProfesorSeleccionado($centroArray);

        //Obtener los grupos pertenecientes dado un profesor
        $grupos = $this->usuarioRepository->findGruposByUsuario(
            $profesor->getNombre(),
            $profesor->getPrimerApellido(),
            $profesor->getSegundoApellido()
        );

        //Mandamos los atributos que vamos a utilizar para grupo
        $gruposArray = array_map(function($grupo) {
            return [
                'id' => $grupo->getId(),
                'letra' => $grupo->getLetra(),
                'horario' => $grupo->getHorario(),
                'diasTeoria' => $grupo->getDiasTeoria(),
                'diasPractica' => $grupo->getDiasPractica(),
                'asignatura' => $grupo->getAsignatura()->getNombre(),
                'asignaturaId' => $grupo->getAsignatura()->getId(),
                'cuatrimestre' => $grupo->getAsignatura()->getCuatrimestre()
            ];
        }, $grupos);
        //creamos un json de los grupos para pasar al javascript
        $gruposJson = json_encode($gruposArray);

        //Obtenemos las lecciones
        $lecciones = $this->leccionRepository->findAll();

        $leccionesArray = array_map(function($leccion) {
            return [
                'id' => $leccion->getId(),
                'titulo' => $leccion->getTitulo(),
                'asignaturaId' => $leccion->getAsignatura()->getId(),
                'modalidad' => $leccion->getModalidad()
            ];
        }, $lecciones);

        $leccionesJson = json_encode($leccionesArray);

        //Obtenemos los festivos nacionales, festivos locales y festivos centro
        $festivosLocales = $this->festivoLocalRepository->findAll();
        $festivosNacionales = $this->festivoNacionalRepository->findAll();
        $festivosCentro = $this->festivoCentroRepository->findAll();

        $festivosLocalesArray = array_map(function($festivoLocal) {
            return [
                'id' => $festivoLocal->getId(),
                'inicio' => $festivoLocal->getInicio(),
                'final' => $festivoLocal->getFinal(),
                'provincia' => $festivoLocal->getProvincia()
            ];
        }, $festivosLocales);

        $festivosNacionalesArray = array_map(function($festivoNacional) {
            return [
                'id' => $festivoNacional->getId(),
                'inicio' => $festivoNacional->getInicio(),
                'final' => $festivoNacional->getFinal()
            ];
        }, $festivosNacionales);

        $festivosCentroArray = array_map(function($festivoCentro) {
            return [
                'id' => $festivoCentro->getId(),
                'inicio' => $festivoCentro->getInicio(),
                'final' => $festivoCentro->getFinal(),
                'nombreCentro' => $festivoCentro->getCentro()->getNombre(),
                'nombreFestivo' => $festivoCentro->getNombre()
            ];
        }, $festivosCentro);

        $festivosLocalesJson = json_encode($festivosLocalesArray);
        $festivosNacionalesJson = json_encode($festivosNacionalesArray);
        $festivosCentroJson = json_encode($festivosCentroArray);

        return $this->render('formularios/calendario.html.twig', [
            'controller_name' => 'FormularioCalendarioController',
            'grupos' => $gruposJson,
            'asignaturas' => $titulosAsignaturas,
            'lecciones' => $leccionesJson,
            'festivosLocales' => $festivosLocalesJson,
            'festivosNacionales' => $festivosNacionalesJson,
            'festivosCentro' => $festivosCentroJson
        ]);
    }
}
