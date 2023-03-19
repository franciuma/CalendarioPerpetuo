<?php

namespace App\Controller;

use App\Entity\Anio;
use App\Entity\Calendario;
use App\Entity\Dia;
use App\Entity\Mes;
use App\Service\FestivoNacionalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FestivoNacionalRepository;


class CalendarioController extends AbstractController
{
    const NMESES = 11;
    const NUM_MES_INICIAL = 9;
    const ANIO = "2023";
    const ANIO_SIGUIENTE = "2024";

    private FestivoNacionalService $festivoNacionalService;
    private FestivoNacionalRepository $festivoNacionalRepository;

    public function __construct(
        FestivoNacionalService $festivoNacionalService,
        FestivoNacionalRepository $festivoNacionalRepository)
    {
        $this->festivoNacionalService = $festivoNacionalService;
        $this->festivoNacionalRepository = $festivoNacionalRepository;
    }

    /**
     * @Route("/calendario", name="calendar")
     */
    public function index(): Response
    {
        $this->festivoNacionalService->getFestivosNacionales();

        $anio = new Anio(self::ANIO);
        $anioSig = new Anio(self::ANIO_SIGUIENTE);
        $calendario = new Calendario();

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
            $mes->setPrimerDia($primerDiaDeMes);
            $ultimoDiaDeMes = intval(self::ultimoDiaMes($mesActual, $anio->getNumAnio()));
            
            for($numDia= 1; $numDia <= $ultimoDiaDeMes; $numDia++) {
                $dia = new Dia($numDia);
                $dia->setFecha($dia->getNumDia()."-".$mes->getNumMes()."-".substr($anio->getNumAnio(), 2, 3));
                $mes->addDia($dia);

                $diaSemana = intval(self::calcularDiaMes($dia->getNumDia(), $mesActual, $anio->getNumAnio()));
                $nombreDiaDeLaSemana = $calendario->getDiasSemana()[$diaSemana];
                $dia->setNombreDiaDeLaSemana($nombreDiaDeLaSemana);

                if($this->festivoNacionalRepository->findOneFecha($dia->getFecha())
                    || $nombreDiaDeLaSemana == "Sab" || $nombreDiaDeLaSemana == "Dom") {
                    $dia->setEsLectivo(true);
                    //$dia->setEvento(pasarleElEvento) Hacer relacion de festivos uno a muchos con dia.
                }
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
}
