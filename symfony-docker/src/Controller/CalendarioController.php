<?php

namespace App\Controller;

use App\Entity\Anio;
use App\Entity\Calendario;
use App\Entity\Dia;
use App\Entity\Evento;
use App\Entity\Mes;
use App\Repository\AnioRepository;
use App\Repository\CalendarioRepository;
use App\Repository\CentroRepository;
use App\Repository\DiaRepository;
use App\Repository\FestivoLocalRepository;
use App\Repository\FestivoNacionalRepository;
use App\Repository\MesRepository;
use App\Repository\FestivoCentroRepository;
use App\Repository\ClaseRepository;
use App\Service\ClaseService;
use App\Repository\UsuarioRepository;
use App\Repository\EventoRepository;
use App\Repository\UsuarioGrupoRepository;
use App\Service\CalendarioService;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarioController extends AbstractController
{
    const NMESES = 9;
    const NUM_MES_INICIAL = 9;

    private $provincia;
    private $centro;
    private $usuario;
    private $anioAc;
    private $anioSig;
    private $tipoUsuario;
    private $clasesAlumno;
    private EventoRepository $eventoRepository;
    private AnioRepository $anioRepository;
    private CalendarioRepository $calendarioRepository;
    private ClaseService $claseService;
    private DiaRepository $diaRepository;
    private FestivoLocalRepository $festivoLocalRepository;
    private FestivoNacionalRepository $festivoNacionalRepository;
    private MesRepository $mesRepository;
    private ClaseRepository $claseRepository;
    private FestivoCentroRepository $festivoCentroRepository;
    private UsuarioRepository $usuarioRepository;
    private CalendarioService $calendarioService;
    private CentroRepository $centroRepository;
    private UsuarioGrupoRepository $usuarioGrupoRepository;

    public function __construct(
        AnioRepository $anioRepository,
        CalendarioRepository $calendarioRepository,
        ClaseService $claseService,
        ClaseRepository $claseRepository,
        DiaRepository $diaRepository,
        FestivoLocalRepository $festivoLocalRepository,
        FestivoNacionalRepository $festivoNacionalRepository,
        MesRepository $mesRepository,
        UsuarioRepository $usuarioRepository,
        FestivoCentroRepository $festivoCentroRepository,
        CalendarioService $calendarioService,
        CentroRepository $centroRepository,
        EventoRepository $eventoRepository,
        UsuarioGrupoRepository $usuarioGrupoRepository
    ) {
        $this->anioRepository = $anioRepository;
        $this->calendarioRepository = $calendarioRepository;
        $this->claseService = $claseService;
        $this->claseRepository = $claseRepository;
        $this->diaRepository = $diaRepository;
        $this->festivoLocalRepository = $festivoLocalRepository;
        $this->festivoNacionalRepository = $festivoNacionalRepository;
        $this->mesRepository = $mesRepository;
        $this->usuarioRepository = $usuarioRepository;
        $this->festivoCentroRepository = $festivoCentroRepository;
        $this->calendarioService = $calendarioService;
        $this->centroRepository = $centroRepository;
        $this->eventoRepository = $eventoRepository;
        $this->usuarioGrupoRepository = $usuarioGrupoRepository;
    }

    #[Route('/calendario', name: 'app_calendario')]
    #[Route('/ver/calendario', name: 'app_ver_calendario')]
    #[Route('/trasladar/calendario', name: 'app_trasladar_calendario')]
    public function index(Request $request): Response
    {
        //Calcular los años actuales y anterior
        self::calcularAnios($request);
        //Obtenemos los datos del POST
        $this->centro = $request->get('centro');
        $this->provincia = $request->get('provincia');
        $this->usuario = $request->get('usuario');
        $this->tipoUsuario = "Profesor";

        $usuario = self::obtenerUsuarioCalendario();
        $calendario = $this->calendarioRepository->findOneByUsuario($usuario->getId());
        $centro = $this->centroRepository->findOneByNombre($this->centro);
        //Actualizamos las variables globales
        $this->centro = $centro;
        $this->usuario = $usuario;
        //Si no se está leyendo un calendario
        if (!($request->getPathInfo() == '/ver/calendario')) {
            // Si no se ha creado el calendario
            if (!$calendario) {
                //Creamos el calendario completo
                $calendario = self::crearCalendarioCompleto($centro);
            //Si se está trasladando el calendario
            } else if($request->getPathInfo() == '/trasladar/calendario'){
                //Borramos el antiguo calendario completamente
                self::eliminarCalendarioCompleto($calendario);
                //Creamos el calendario completo con los años nuevos.
                $calendario = self::crearCalendarioCompleto($centro);
            } else {
                //Editamos el calendario existente        
                self::editarClasesCalendario($calendario);
            }
        }

        return $this->render('calendario/index.html.twig', [
            'calendario' => $calendario,
            'dias_semana' => $calendario->getDiasSemana()
        ]);
    }

    /**
     * Crea el calendario de un alumno
     */
    #[Route('/calendario/alumno', name: 'app_calendario_alumno')]
    public function CalendarioAlumno(Request $request): Response
    {
        //Calcular los años actuales y anterior
        self::calcularAnios($request);
        //Obtenemos los datos del POST
        $dni = $request->get("dni");
        $alumno = $this->usuarioRepository->findOneByDni($dni);
        $usuarioGrupos = $this->usuarioGrupoRepository->findUsuarioGrupoByUsuarioId($alumno->getId());
        $centro = $usuarioGrupos[0]->getGrupo()->getAsignatura()->getTitulacion()->getCentro();
        $this->tipoUsuario = "Alumno";
        $this->centro = $centro;
        $this->provincia = $centro->getProvincia();
        $this->usuario = $alumno;

        $clases = [];
        foreach ($usuarioGrupos as $usuarioGrupo) {
            $grupoAlumno = $usuarioGrupo->getGrupo()->getId();
            $asignaturaAlumno = $usuarioGrupo->getGrupo()->getAsignatura()->getId();

            $clases = array_merge($clases, $this->claseRepository->findByAsignaturaGrupo($asignaturaAlumno, $grupoAlumno));
        }
        $this->clasesAlumno = $clases;

        if($centro == "") {
            throw new Exception("El alumno no tiene grupos asociados");
        }

        $calendario = self::crearCalendarioCompleto($centro);

        return $this->render('calendario/index.html.twig', [
            'calendario' => $calendario,
            'dias_semana' => $calendario->getDiasSemana()
        ]);
    }

    /**
     * Elimina un calendario por completo a partir de la entidad
     */
    public function eliminarCalendarioCompleto($calendario)
    {
        //Borramos los eventos de clase
        $eventosClases = $this->eventoRepository->findEventoClaseByCalendario($calendario);
        $this->eventoRepository->removeEventos($eventosClases);
        //Borramos el calendario antiguo
        $this->calendarioRepository->remove($calendario);
        $this->eventoRepository->flush();
    }

    /**
     * Crea un calendario completo a partir de un centro
     */
    public function crearCalendarioCompleto($centro)
    {
        //Creamos el calendario y lo obtenemos
        $calendario = $this->calendarioService->getCalendario($this->usuario, $centro, $this->tipoUsuario);
        if($this->tipoUsuario == "Profesor") {
            $this->claseService->getClases($calendario, true);
        }
        //Crea los años del calendario
        $anios = self::creacionAnios($calendario);
        //Crea toda la estructura del calendario
        self::creacionCalendario($anios, $calendario);

        return $calendario;
    }

    /**
     *  Calcula los años actual y anterior en base a los meses actuales.
     *  Siempre que se cree un calendario, este será para el año actual y el siguiente.
     */
    public function calcularAnios(Request $request): array
    {
        $centroJson = file_get_contents(__DIR__ . '/../resources/centro.json');
        $centroArray = json_decode($centroJson, true);
        //Si está el curso en centro.json, se está trasladando el calendario.
        if($request->getPathInfo() == '/trasladar/calendario') {
            $curso = $centroArray[0]["curso"];
            $curso = explode("/",$curso);
            $this->anioAc = $curso[0];
            $this->anioSig = $curso[1];
        } else {
            // Si no está el curso en centro.json, se está creando el calendario nuevo.
            $fechaHoy = new DateTime();
            $aniofechaHoy = $fechaHoy->format('Y');
    
            $this->anioAc = $aniofechaHoy;
            $this->anioSig = intval($aniofechaHoy) + 1;
        }

        return [$this->anioAc, $this->anioSig];
    }

    /**
     * Crea los años anterior y siguiente del calendario en base al año actual.
     */
    public function creacionAnios(Calendario $calendario): array
    {
        $anios = [];

        $anio = new Anio($this->anioAc);
        $anioSig = new Anio($this->anioSig);

        $anio->setCalendario($calendario);
        $anioSig->setCalendario($calendario);

        if($this->tipoUsuario == "Profesor") {
            $this->anioRepository->save($anio);
            $this->anioRepository->save($anioSig);
        }

        array_push($anios, $anio);
        array_push($anios, $anioSig);

        return $anios;
    }

    /**
     * Crea los meses, años y días del calendario y los persiste a la bd.
     * Es básicamente la estructura del calendario.
     */
    public function creacionCalendario($anios, Calendario $calendario): void
    {
        $anio = $anios[0];
        $anioSig = $anios[1];

        for ($numMes = self::NUM_MES_INICIAL; $numMes <= self::NMESES + self::NUM_MES_INICIAL; $numMes++) {

            $mesActual = $numMes % 12;
            $mesActual = $mesActual === 0 ? 12 : $mesActual;

            if ($numMes == 13) {
                $calendario->addAnio($anio);
                $anio = $anioSig;
            }

            $mes = new Mes($mesActual);
            $anio->addMes($mes);
            $mes->setNombre($calendario->getMeses()[$mesActual]);
            $primerDiaDeMes = intval(self::calcularDiaDeLaSemana(1, $mesActual, $anio->getNumAnio()));
            $ultimoDiaDeMes = intval(self::ultimoDiaMes($mesActual, $anio->getNumAnio()));
            $mes->setPrimerDia($primerDiaDeMes);
            $mes->setAnio($anio);
            if($this->tipoUsuario == "Profesor") {
                $this->mesRepository->save($mes);
            }

            for ($numDia = 1; $numDia <= $ultimoDiaDeMes; $numDia++) {
                $dia = new Dia($numDia);
                $dia->setFecha($dia->getNumDia() . "-" . $mes->getNumMes() . "-" . substr($anio->getNumAnio(), 2, 3));
                $mes->addDia($dia);

                $diaSemana = intval(self::calcularDiaDeLaSemana($dia->getNumDia(), $mesActual, $anio->getNumAnio()));
                $nombreDiaDeLaSemana = $calendario->getDiasSemana()[$diaSemana];
                $dia->setNombreDiaDeLaSemana($nombreDiaDeLaSemana);

                self::colocarEventos($dia, $nombreDiaDeLaSemana, $calendario);

                $dia->setMes($mes);
                //Almacenamos los días
                if($this->tipoUsuario == "Profesor") {
                    $this->diaRepository->save($dia);
                }
            }
        }
        //Persistimos todas las entidades guardadas
        if($this->tipoUsuario == "Profesor") {
            $this->diaRepository->flush();
        }
        $calendario->addAnio($anio);
    }

    /**
     * Función que calcula el último día del mes en un año determinado.
     * El formato devuelto es un entero.
     */
    public function ultimoDiaMes($mes, $anio)
    {
        return date('d', mktime(0, 0, 0, $mes + 1, 0, $anio));
    }

    /**
     * Función que calcula el día de la semana del mes correspondiente en un año determinado.
     * El formato devuelto es un número del 0 al 6, correspondiendo el 0 al lunes y el 6 al domingo.
     */
    public function calcularDiaDeLaSemana($dia, $mes, $anio)
    {
        return date('N', mktime(0, 0, 0, $mes, $dia, $anio)) - 1;
    }

    /**
     * Función dedicada a colocar los eventos en el calendario.
     * Un evento es una clase (lección), o un festivo.
     */
    public function colocarEventos(Dia $dia, $nombreDiaDeLaSemana, Calendario $calendario)
    {
        if($this->tipoUsuario == "Profesor") {
            $clases = $this->claseRepository->findByFecha($dia->getFecha(), $calendario->getId());
        } else {
            $clases = [];
            foreach ($this->clasesAlumno as $clase) {
                if($clase->getFecha() == $dia->getFecha()) {
                    $clases[] = $clase;
                }
            }
        }

        $festivoNacional = $this->festivoNacionalRepository->findOneFecha($dia->getFecha());
        $festivoLocal = $this->festivoLocalRepository->findOneFechaProvincia($dia->getFecha(), $this->provincia);
        //$provinciafestivoLocal = $festivoLocal ? $festivoLocal->getProvincia() : null;

        //$usuario = self::obtenerUsuarioCalendario();
        //$centro = $this->centroRepository->findOneByUsuario($usuario->getId());
        $festivoCentro = $this->festivoCentroRepository->findOneFechaCentro($dia->getFecha(), $this->centro->getId());
        $festivoCentroCuatrimestre = $this->festivoCentroRepository->findOneFechaFinalCentro($dia->getFecha(), $this->centro->getId());
        //$centroNombre = $festivoCentro || $festivoCentroCuatrimestre ? $centro->getNombre() : null;

        //Si es clase y pertenece al mismo calendario.
        if(count($clases) != 0) {
            foreach ($clases as $clase) {
                if(($calendario->getId() == $clase->getCalendarioId()) || $this->tipoUsuario == "Alumno") {
                    $evento = new Evento($clase);
                    $dia->addEvento($evento);
                }
            }
            $dia->setHayClase(true);
        } else if ($festivoNacional || $festivoLocal || $festivoCentro || $festivoCentroCuatrimestre)
        {
            //Verificamos cual de los festivos no es nulo
            $evento = new Evento($festivoLocal ?? $festivoNacional ?? $festivoCentroCuatrimestre ?? $festivoCentro);
            $dia->setEsNoLectivo(true);
            $dia->addEvento($evento);
        } else if ($nombreDiaDeLaSemana == "Sab" || $nombreDiaDeLaSemana == "Dom") {
            $dia->setEsNoLectivo(true);
        }
    }

    /**
     * Obtiene el objeto Usuario del calendario actual
     */
    public function obtenerUsuarioCalendario()
    {
        if($this->tipoUsuario == "Profesor") {
            //Obtenemos el usuario
            $nombreCompleto = explode(" ", $this->usuario);
            //Asignamos el nombre y apellidos
            $apellidoPr = $nombreCompleto[count($nombreCompleto) - 2];
            $apellidoSeg = $nombreCompleto[count($nombreCompleto) - 1];
            $nombre = implode(" ", array_slice($nombreCompleto, 0, count($nombreCompleto) - 2));

            //Obtenemos el usuario
            $usuario = $this->usuarioRepository->findOneByNombreApellidos($nombre, $apellidoPr, $apellidoSeg);
        } else {
            $usuario = $this->usuarioRepository->findOneByDni($this->usuario);
        }

        return $usuario;
    }

    /**
     * Función para editar las clases y modificar un calendario existente.
     */
    public function editarClasesCalendario($calendario)
    {
        //Obtenemos las clases actuales
        $clasesActuales = $this->claseRepository->findByCalendario($calendario->getId());

        //Obtenemos las clases editadas (vienen en el JSON)
        $clasesEditadas = $this->claseService->getClases($calendario, false);

        //Vemos que clases actuales no están en las clases editadas, para así borrarlas
        $claseActualEncontrada = false;
        foreach ($clasesActuales as $claseActual) {
            $claseActualEncontrada = false;
            foreach ($clasesEditadas as $claseEditada) {
                if ($claseActual->getFecha() === $claseEditada->getFecha()
                    && $claseActual->getNombre() === $claseEditada->getNombre()
                    && $claseActual->getGrupo()->getId() === $claseEditada->getGrupo()->getId()
                ) {
                    $claseActualEncontrada = true;
                    break;
                }
            }

            if (!$claseActualEncontrada) {
                //Buscamos el día al que pertenece y ponemos que no hay clase
                $dia = $this->diaRepository->findOneByFecha($claseActual->getFecha(), $calendario->getId());
                //Buscamos el evento y lo borramos, en cascada se borrará la clase asociada
                $evento = $this->eventoRepository->findByClaseId($claseActual->getId());
                //Configuramos dia
                $dia->setHayClase(false);
                $dia->removeEvento($evento);
                //Eliminamos el evento
                $this->eventoRepository->remove($evento);
                //Guardamos la información del día
                $this->diaRepository->save($dia);
            }
        }

        //Ahora vemos que clases editadas no están en la base de datos
        foreach ($clasesEditadas as $claseEditada) {
            //Buscamos la clase actual en las clases editadas
            $nuevaClase = $this->claseRepository->findClaseByFechaNombreGrupo(
                $claseEditada->getFecha(),
                $claseEditada->getNombre(),
                $claseEditada->getGrupo()->getId()
            );
            //Si la clase no está se persiste, incluido su evento
            if(is_null($nuevaClase)) {
                //Buscamos el día al que pertenece y añadimos el evento
                $evento = new Evento($claseEditada);
                $dia = $this->diaRepository->findOneByFecha($claseEditada->getFecha(), $calendario->getId());
                $dia->setHayClase(true);
                $dia->addEvento($evento);
                //Persistimos el dia y la clase
                $this->diaRepository->save($dia);
                $this->claseRepository->save($claseEditada);                
            }
        }

        //Actualizamos la bd
        $this->diaRepository->flush();
    }
}
