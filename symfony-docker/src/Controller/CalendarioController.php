<?php

namespace App\Controller;

use App\Entity\Anio;
use App\Entity\Calendario;
use App\Entity\Dia;
use App\Entity\Evento;
use App\Entity\FestivoCentro;
use App\Entity\Mes;
use App\Repository\AnioRepository;
use App\Repository\CalendarioRepository;
use App\Repository\DiaRepository;
use App\Repository\FestivoLocalRepository;
use App\Repository\FestivoNacionalRepository;
use App\Repository\MesRepository;
use App\Repository\FestivoCentroRepository;
use App\Repository\ClaseRepository;
use App\Service\ClaseService;
use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarioController extends AbstractController
{
    const ANIO = "2023";
    const ANIO_SIGUIENTE = "2024";
    const NMESES = 9;
    const NUM_MES_INICIAL = 9;

    private $provincia;
    private $centro;
    private $usuario;
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
        FestivoCentroRepository $festivoCentroRepository
    ) {
        $this->provincia = isset($_GET['provincia']) ? $_GET['provincia'] : null;
        $this->usuario = $_GET['usuario'];
        $this->centro = isset($_GET['centro']) ? $_GET['centro'] : null;

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
    }

    /**
     * @Route("/calendario", name="calendar")
     */
    public function index(): Response
    {
        //Obtenemos el usuario
        $nombreCompleto = explode(" ", $this->usuario);
        //Asignamos el nombre y apellidos
        $nombre = $nombreCompleto[0];
        $apellidoPr = $nombreCompleto[1];
        $apellidoSeg = $nombreCompleto[2];

        $usuario = $this->usuarioRepository->findOneByNombreApellidos($nombre, $apellidoPr, $apellidoSeg);
        $calendario = $this->calendarioRepository->findOneByUsuario($usuario->getId());

        // Si el usuario que se ha pasado no está relacionado con un calendario
        // Si no hay clases metidas en ese calendario, el calendario no ha sido creado aún
        if (!$this->claseRepository->findOneByCalendario($calendario->getId())) {
            $this->claseService->getClases($calendario);
            //Crea los años del calendario
            $anios = self::creacionAnios($calendario);
            //Crea todo el calendario
            self::creacionCalendario($anios, $calendario);
        }

        return $this->render('calendario/index.html.twig', [
            'calendario' => $calendario,
            'dias_semana' => $calendario->getdiasSemana(),
        ]);
    }

    /**
     * Crea los años anterior y siguiente del calendario en base al año actual.
     */
    public function creacionAnios(Calendario $calendario): array
    {
        $anios = [];

        $anio = new Anio(self::ANIO);
        $anioSig = new Anio(self::ANIO_SIGUIENTE);

        $anio->setCalendario($calendario);
        $anioSig->setCalendario($calendario);

        $this->anioRepository->save($anio, true);
        $this->anioRepository->save($anioSig, true);

        array_push($anios, $anio);
        array_push($anios, $anioSig);

        return $anios;
    }

    /**
     * Crea los meses, años y días del calendario y los persiste a la bd.
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
            $this->mesRepository->save($mes, true);

            for ($numDia = 1; $numDia <= $ultimoDiaDeMes; $numDia++) {
                $dia = new Dia($numDia);
                $dia->setFecha($dia->getNumDia() . "-" . $mes->getNumMes() . "-" . substr($anio->getNumAnio(), 2, 3));
                $mes->addDia($dia);

                $diaSemana = intval(self::calcularDiaDeLaSemana($dia->getNumDia(), $mesActual, $anio->getNumAnio()));
                $nombreDiaDeLaSemana = $calendario->getDiasSemana()[$diaSemana];
                $dia->setNombreDiaDeLaSemana($nombreDiaDeLaSemana);

                self::colocarEventos($dia, $nombreDiaDeLaSemana, $calendario);

                $dia->setMes($mes);
                $this->diaRepository->save($dia, true);
            }
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
        $clase = $this->claseRepository->findOneFecha($dia->getFecha(), $calendario->getId());
        $festivoNacional = $this->festivoNacionalRepository->findOneFecha($dia->getFecha());
        $festivoLocal = $this->festivoLocalRepository->findOneFecha($dia->getFecha());
        $provinciafestivoLocal = $festivoLocal ? $festivoLocal->getProvincia() : null;
        $festivoCentro = $this->festivoCentroRepository->findOneFechaCentro($dia->getFecha(), $this->centro);
        $centroNombre = $festivoCentro ? $festivoCentro->getCentro()->getNombre() : null;

        //Si es clase y pertenece al mismo calendario.
        if($clase && ($calendario->getId() == $clase->getCalendarioId())) {
            $evento = new Evento($clase);
            $dia->setHayClase(true);
            $dia->setEvento($evento);
        } else if (
            $festivoNacional
            || ($festivoLocal && $this->provincia == $provinciafestivoLocal)
            || ($festivoCentro && $this->centro == $centroNombre)
        ) {
            //Verificamos cual de los festivos no es nulo
            $evento = new Evento($festivoLocal ?? $festivoNacional ?? $festivoCentro);
            $dia->setEsNoLectivo(true);
            $dia->setEvento($evento);
        } else if ($nombreDiaDeLaSemana == "Sab" || $nombreDiaDeLaSemana == "Dom") {
            $dia->setEsNoLectivo(true);
        }
    }
}
