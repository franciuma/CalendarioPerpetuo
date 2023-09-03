<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AsignaturaRepository;
use App\Repository\CalendarioRepository;
use App\Repository\CentroRepository;
use App\Repository\ClaseRepository;
use App\Repository\FestivoCentroRepository;
use App\Repository\FestivoLocalRepository;
use App\Repository\FestivoNacionalRepository;
use App\Repository\LeccionRepository;
use App\Service\CalendarioService;
use App\Repository\UsuarioRepository;
use DateTime;
use Symfony\Component\HttpFoundation\Request;

class FormularioCalendarioController extends AbstractController
{

    private AsignaturaRepository $asignaturaRepository;
    private LeccionRepository $leccionRepository;
    private FestivoLocalRepository $festivoLocalRepository;
    private FestivoNacionalRepository $festivoNacionalRepository;
    private FestivoCentroRepository $festivoCentroRepository;
    private CalendarioService $calendarioService;
    private UsuarioRepository $usuarioRepository;
    private CentroRepository $centroRepository;
    private ClaseRepository $claseRepository;
    private CalendarioRepository $calendarioRepository;

    public function __construct(
        AsignaturaRepository $asignaturaRepository,
        LeccionRepository $leccionRepository,
        FestivoLocalRepository $festivoLocalRepository,
        FestivoNacionalRepository $festivoNacionalRepository,
        FestivoCentroRepository $festivoCentroRepository,
        CalendarioService $calendarioService,
        UsuarioRepository $usuarioRepository,
        CentroRepository $centroRepository,
        ClaseRepository $claseRepository,
        CalendarioRepository $calendarioRepository
        ){
        $this->asignaturaRepository = $asignaturaRepository;
        $this->leccionRepository = $leccionRepository;
        $this->festivoLocalRepository = $festivoLocalRepository;
        $this->festivoNacionalRepository = $festivoNacionalRepository;
        $this->festivoCentroRepository = $festivoCentroRepository;
        $this->calendarioService = $calendarioService;
        $this->usuarioRepository = $usuarioRepository;
        $this->centroRepository = $centroRepository;
        $this->claseRepository = $claseRepository;
        $this->calendarioRepository = $calendarioRepository;
    }

    #[Route('/formulario/calendario', name: 'app_formulario_calendario')]
    #[Route('/formulario/trasladar/calendario', name: 'app_formulario_trasladar_calendario')]
    #[Route('/formulario/editar/calendario', name: 'app_formulario_editar_calendario')]
    public function index(): Response
    {
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

        $centroJson = file_get_contents(__DIR__ . '/../resources/centro.json');
        $centroArray = json_decode($centroJson, true);
        $nombreProfesor = $centroArray[0]['profesor'];
        //Obtenemos profesor introducido en la base de datos
        $profesor = $this->calendarioService->getProfesorSeleccionado($nombreProfesor);

        //Obtener las asignaturas del docente:
        $gruposProfesor = $this->usuarioRepository->findGruposByUsuario($profesor->getNombre(), $profesor->getPrimerApellido(), $profesor->getSegundoApellido());
        $asignaturas = [];
        foreach ($gruposProfesor as $gruposProfesor) {
            $asignaturas[] = $gruposProfesor->getAsignatura();
        }

        $asignaturasArray = array_map(function($asignatura) {
            return [
                'id' => $asignatura->getId(),
                'asignatura' => $asignatura->getNombre()
            ];
        }, $asignaturas);

        $asignaturasArray = array_unique($asignaturasArray, SORT_REGULAR);

        //creamos un json de las asignaturas para pasar al javascript
        $asignaturasJson = json_encode($asignaturasArray);

        $clasesJson = "";
        $cursoJson = json_encode(self::calcularCursoActual());
        //Si está el editar, es que se está editando o trasladando un calendario
        if(isset($centroArray[0]['editar'])) {
            $centroObjeto = $this->centroRepository->findOneByUsuario($profesor->getId());
            $centro = $centroObjeto->getNombre();
            $provincia = $centroObjeto->getProvincia();
            //Obtenemos el curso y le damos formato si existe
            if(isset($centroArray[0]['curso'])) {
                $curso = explode("/", $centroArray[0]['curso']);
                $anio = substr($curso[0], 2, 3);
                $anioSiguiente = substr($curso[1], 2, 3);
                $cursoCompleto = [$anio, $anioSiguiente];
                $cursoJson = json_encode($cursoCompleto);
            }
            // Obtenemos el calendario existente
            $calendario = $this->calendarioRepository->findOneByUsuario($profesor->getId());
            //Obtenemos las clases asociadas a ese calendario
            $clases = $this->claseRepository->findByCalendario($calendario->getId());
            //Mandamos los atributos que vamos a utilizar para clases
            $clasesArray = array_map(function($clase) {
                return [
                    'id' => $clase->getId(),
                    'fecha' => $clase->getFecha(),
                    'nombre' => $clase->getNombre(),
                    'asignaturaId' => $clase->getAsignatura()->getId(),
                    'modalidad' => $clase->getModalidad(),
                    'asignaturaNombre' => $clase->getAsignatura()->getNombre(),
                    'letraGrupo' => $clase->getGrupo()->getLetra(),
                    'modalidad' => $clase->getModalidad(),
                    'horario' => $clase->getGrupo()->getHorario(),
                    'enlace' => $clase->getEnlace()
                ];
            }, $clases);
            //creamos un json de los grupos para pasar al javascript
            $clasesJson = json_encode($clasesArray);
        } else {
            $centro = $centroArray[0]['nombre'];
            $provincia = $centroArray[0]['provincia'];
        }

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
            'asignaturas' => $asignaturasJson,
            'lecciones' => $leccionesJson,
            'festivosLocales' => $festivosLocalesJson,
            'festivosNacionales' => $festivosNacionalesJson,
            'festivosCentro' => $festivosCentroJson,
            'centro' => $centro,
            'provincia' => $provincia,
            'clases' => $clasesJson,
            'curso' => $cursoJson
        ]);
    }

    public function calcularCursoActual(): array
    {
        $fechaHoy = new DateTime();
        $aniofechaHoy = $fechaHoy->format('Y');
        $anioSiguiente = intval($aniofechaHoy) + 1;

        $anioActualFormato = substr($aniofechaHoy, 2, 3);
        $anioSiguienteFormato = substr(strval($anioSiguiente), 2, 3);
        return [$anioActualFormato, $anioSiguienteFormato];
    }
}
