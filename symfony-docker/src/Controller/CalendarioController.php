<?php

namespace App\Controller;

use App\Entity\Anio;
use App\Entity\Calendario;
use App\Entity\Dia;
use App\Entity\Evento;
use App\Entity\Mes;
use App\Repository\AnioRepository;
use App\Repository\CalendarioRepository;
use App\Repository\DiaRepository;
use App\Repository\FestivoLocalRepository;
use App\Repository\FestivoNacionalRepository;
use App\Repository\MesRepository;
use App\Service\FestivoLocalService;
use App\Service\FestivoNacionalService;
use App\Repository\ClaseRepository;
use App\Service\ClaseService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarioController extends AbstractController
{
    const ANIO = "2023";
    const ANIO_SIGUIENTE = "2024";
    const NMESES = 9;
    const NUM_MES_INICIAL = 9;

    private $nombreCalendario;
    private $provincia;
    private $persistirBd = false;
    private AnioRepository $anioRepository;
    private CalendarioRepository $calendarioRepository;
    private ClaseService $claseService;
    private DiaRepository $diaRepository;
    private FestivoLocalRepository $festivoLocalRepository;
    private FestivoLocalService $festivoLocalService;
    private FestivoNacionalRepository $festivoNacionalRepository;
    private FestivoNacionalService $festivoNacionalService;
    private MesRepository $mesRepository;
    private ClaseRepository $claseRepository;

    public function __construct(
        AnioRepository $anioRepository,
        CalendarioRepository $calendarioRepository,
        ClaseService $claseService,
        ClaseRepository $claseRepository,
        DiaRepository $diaRepository,
        FestivoLocalRepository $festivoLocalRepository,
        FestivoLocalService $festivoLocalService,
        FestivoNacionalRepository $festivoNacionalRepository,
        FestivoNacionalService $festivoNacionalService,
        MesRepository $mesRepository
    ) {
        $this->provincia = $_GET['provincia'];
        $this->nombreCalendario = $_GET['centro'];

        $this->anioRepository = $anioRepository;
        $this->calendarioRepository = $calendarioRepository;
        $this->claseService = $claseService;
        $this->claseRepository = $claseRepository;
        $this->diaRepository = $diaRepository;
        $this->festivoLocalRepository = $festivoLocalRepository;
        $this->festivoLocalService = $festivoLocalService;
        $this->festivoNacionalRepository = $festivoNacionalRepository;
        $this->festivoNacionalService = $festivoNacionalService;
        $this->mesRepository = $mesRepository;
    }

    /**
     * @Route("/calendario", name="calendar")
     */
    public function index(): Response
    {
        $calendario = new Calendario($this->nombreCalendario, $this->provincia);

        if (
            !$this->calendarioRepository->findOneByNombre($this->nombreCalendario)
            || !$this->calendarioRepository->findOneByProvincia($this->provincia)
            || !$this->anioRepository->findOneByNumAnio(self::ANIO) &&
            !$this->anioRepository->findOneByNumAnio(self::ANIO_SIGUIENTE)
        ) {
            $this->persistirBd = true;
            $this->calendarioRepository->save($calendario, $this->persistirBd);
        }

        self::colocarEventosBd($calendario);

        $anio = new Anio(self::ANIO);
        $anioSig = new Anio(self::ANIO_SIGUIENTE);

        $anio->setCalendario($calendario);
        $anioSig->setCalendario($calendario);

        $this->anioRepository->save($anio, $this->persistirBd);
        $this->anioRepository->save($anioSig, $this->persistirBd);

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
            $this->mesRepository->save($mes, $this->persistirBd);

            for ($numDia = 1; $numDia <= $ultimoDiaDeMes; $numDia++) {
                $dia = new Dia($numDia);
                $dia->setFecha($dia->getNumDia() . "-" . $mes->getNumMes() . "-" . substr($anio->getNumAnio(), 2, 3));
                $mes->addDia($dia);

                $diaSemana = intval(self::calcularDiaDeLaSemana($dia->getNumDia(), $mesActual, $anio->getNumAnio()));
                $nombreDiaDeLaSemana = $calendario->getDiasSemana()[$diaSemana];
                $dia->setNombreDiaDeLaSemana($nombreDiaDeLaSemana);

                self::colocarEventos($dia, $nombreDiaDeLaSemana, $calendario);

                $dia->setMes($mes);
                $this->diaRepository->save($dia, $this->persistirBd);
            }
        }
        $calendario->addAnio($anio);

        return $this->render('calendario/index.html.twig', [
            'calendario' => $calendario,
            'dias_semana' => $calendario->getdiasSemana(),
        ]);
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
        $provinciaEventoLocal = $festivoLocal ? $festivoLocal->getProvincia() : null;

        //Si es clase y pertenece al mismo calendario.
        if($clase && ($calendario->getId() == $clase->getCalendarioId())) {
            $evento = new Evento($clase);
            $dia->setHayClase(true);
            $dia->setEvento($evento);
        } else if ($festivoNacional || ($festivoLocal && $this->provincia == $provinciaEventoLocal)) {
            $evento = new Evento($festivoLocal ?? $festivoNacional);
            $dia->setEsNoLectivo(true);
            $dia->setEvento($evento);
        } else if ($nombreDiaDeLaSemana == "Sab" || $nombreDiaDeLaSemana == "Dom") {
            $dia->setEsNoLectivo(true);
        }
    }

    /**
     * Función dedicada a colocar los eventos en la base de datos (festivos y clases).
     */
    public function colocarEventosBd(Calendario $calendario)
    {
        if($this->persistirBd){
            $this->festivoNacionalService->getFestivosNacionales();
            $this->festivoLocalService->getFestivosLocales($calendario);
            $this->claseService->getClases($calendario);
        }
    }
}
