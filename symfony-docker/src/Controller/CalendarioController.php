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

        $anio = new Anio(date('Y'));
        $calendario = new Calendario($anio->getNumAnio());

        for ($numMes = 0; $numMes <= 5; $numMes++) {
            $mesActual = date('n')+$numMes;
            $mes = new Mes($mesActual);
            $anio->addMes($mes);
            $mes->setNombre($calendario->getMeses()[$mesActual]);
            $primerDiaDeMes = intval(self::calcularDiaMes(1, $mesActual, $anio->getNumAnio()));
            $mes->setPrimerDia($primerDiaDeMes);
            $ultimoDiaDeMes = intval(self::ultimoDiaMes($mesActual, $anio->getNumAnio()));
            
            for($numDia= 1; $numDia <= $ultimoDiaDeMes; $numDia++) {
                $dia = new Dia($numDia);
                $dia->setFecha($dia->getValor()."-".$mes->getNumMes()."-".substr($anio->getNumAnio(), 2, 3));
                $mes->addDia($dia);

                $diaMes = intval(self::calcularDiaMes($dia->getValor(), $mesActual, $anio->getNumAnio()));
                $nombreDiaDeLaSemana = $calendario->getDiasSemana()[$diaMes];
                $dia->setNombreDiaDeLaSemana($nombreDiaDeLaSemana);

                if($this->festivoNacionalRepository->findOneFecha($dia->getFecha())
                    || $nombreDiaDeLaSemana == "Sab" || $nombreDiaDeLaSemana == "Dom") {
                    $dia->setIsLectivo(true);
                    //$dia->setEvento(pasarleElEvento) Hacer relacion de festivos uno a muchos con dia.
                }
            }
        }

        return $this->render('calendario/index.html.twig', [
            'anio' => $anio,
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
