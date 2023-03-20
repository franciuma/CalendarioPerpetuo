<?php

namespace App\Controller;

use App\Entity\Anio;
use App\Entity\Calendario;
use App\Entity\Dia;
use App\Entity\Mes;
use App\Repository\AnioRepository;
use App\Repository\CalendarioRepository;
use App\Repository\DiaRepository;
use App\Repository\FestivoNacionalRepository;
use App\Repository\MesRepository;
use App\Service\FestivoNacionalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarioController extends AbstractController
{
    const NMESES = 11;
    const NUM_MES_INICIAL = 9;
    const ANIO = "2023";
    const ANIO_SIGUIENTE = "2024";

    private $persistirBd = false;
    private AnioRepository $anioRepository;
    private CalendarioRepository $calendarioRepository;
    private DiaRepository $diaRepository;
    private FestivoNacionalRepository $festivoNacionalRepository;
    private FestivoNacionalService $festivoNacionalService;
    private MesRepository $mesRepository;

    public function __construct(
        AnioRepository $anioRepository,
        CalendarioRepository $calendarioRepository,
        DiaRepository $diaRepository,
        FestivoNacionalRepository $festivoNacionalRepository,
        FestivoNacionalService $festivoNacionalService,
        MesRepository $mesRepository
    )
    {
        $this->anioRepository = $anioRepository;
        $this->calendarioRepository = $calendarioRepository;
        $this->diaRepository = $diaRepository;
        $this->festivoNacionalRepository = $festivoNacionalRepository;
        $this->festivoNacionalService = $festivoNacionalService;
        $this->mesRepository = $mesRepository;
    }

    /**
     * @Route("/calendario", name="calendar")
     */
    public function index(): Response
    {
        $this->festivoNacionalService->getFestivosNacionales();

        $nombreCalendario = "Calendario Teleco"; //posteriormente pasarlo por parametro (formulario quizas o json)
        $calendario = new Calendario($nombreCalendario);
        
        if(!$this->calendarioRepository->findOneByNombre($nombreCalendario)){
            $this->persistirBd = true;
            $this->calendarioRepository->save($calendario,$this->persistirBd);
        }

        $anio = new Anio(self::ANIO);
        $anioSig = new Anio(self::ANIO_SIGUIENTE);

        $anio->setCalendario($calendario);
        $anioSig->setCalendario($calendario);

        $this->anioRepository->save($anio,$this->persistirBd);
        $this->anioRepository->save($anioSig,$this->persistirBd);

        for ($numMes = self::NUM_MES_INICIAL; $numMes <= self::NMESES + self::NUM_MES_INICIAL; $numMes++) {

            $mesActual = $numMes % 12;
            $mesActual = $mesActual === 0 ? 12 : $mesActual;

            if($numMes == 13){
                $calendario->addAnio($anio);
                $anio = $anioSig;
            }

            $mes = new Mes($mesActual);
            $anio->addMes($mes);
            $mes->setNombre($calendario->getMeses()[$mesActual]);
            $primerDiaDeMes = intval(self::calcularDiaMes(1, $mesActual, $anio->getNumAnio()));
            $ultimoDiaDeMes = intval(self::ultimoDiaMes($mesActual, $anio->getNumAnio()));
            $mes->setPrimerDia($primerDiaDeMes);
            $mes->setAnio($anio);
            $this->mesRepository->save($mes,$this->persistirBd);
            
            for($numDia= 1; $numDia <= $ultimoDiaDeMes; $numDia++) {
                $dia = new Dia($numDia);
                $dia->setFecha($dia->getNumDia()."-".$mes->getNumMes()."-".substr($anio->getNumAnio(), 2, 3));
                $mes->addDia($dia);

                $diaSemana = intval(self::calcularDiaMes($dia->getNumDia(), $mesActual, $anio->getNumAnio()));
                $nombreDiaDeLaSemana = $calendario->getDiasSemana()[$diaSemana];
                $dia->setNombreDiaDeLaSemana($nombreDiaDeLaSemana);

                self::colocarEventos($dia, $nombreDiaDeLaSemana);

                $dia->setMes($mes);
                $this->diaRepository->save($dia,$this->persistirBd);
            }
        }
        $calendario->addAnio($anio);

        return $this->render('calendario/index.html.twig', [
            'calendario' => $calendario,
            'dias_semana' => $calendario->getdiasSemana(),
        ]);
    }

    public function ultimoDiaMes($mes, $anio) {
        return date('d', mktime(0, 0, 0, $mes + 1, 0, $anio));
    }

    public function calcularDiaMes($dia, $mes, $anio) {
        return date('N', mktime(0, 0, 0, $mes, $dia, $anio)) - 1;
    }

    public function colocarEventos($dia, $nombreDiaDeLaSemana) { // Colocar las clases en un futuro aqui

        $evento = $this->festivoNacionalRepository->findOneFecha($dia->getFecha());

        if($nombreDiaDeLaSemana == "Sab" || $nombreDiaDeLaSemana == "Dom") {
            $dia->setEsLectivo(true);
            $dia->setEvento($evento);
        }

        if($evento) {
            $dia->setEsLectivo(true);
            $dia->setEvento($evento);
            $dia->setNombreEvento($evento->getAbreviatura());
        }
    }
}
