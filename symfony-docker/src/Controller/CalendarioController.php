<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Calendario;

class CalendarioController extends AbstractController
{
    /**
     * @Route("/calendario", name="calendar")
     */
    public function index(): Response
    {
        $mes = date('n');
        $anio = date('Y');

        $calendario = new Calendario($mes,$anio);
        $primerDiaDeCadaMes = [];
        $ultimoDiaDeCadaMes = [];

        for ($i=0; $i <= 5; $i++) { 
            array_push($primerDiaDeCadaMes, self::primerDiaMes(1, $mes+$i, $anio));
            array_push($ultimoDiaDeCadaMes, self::ultimoDiaMes($mes+$i, $anio));
        }

        return $this->render('calendario/index.html.twig', [
            'mes' => $mes,
            'ano' => $anio,
            'meses' => $calendario->getMeses(),
            'dias_semana' => $calendario->getdiasSemana(),
            'primer_dia_de_cada_mes' => $primerDiaDeCadaMes,
            'ultimo_dia_de_cada_mes' => $ultimoDiaDeCadaMes,
        ]);
    }

    public function ultimoDiaMes($mes, $anio) {
        return date('d', mktime(0, 0, 0, $mes + 1, 0, $anio));
    }

    public function primerDiaMes($dia, $mes, $anio) {
        return date('N', mktime(0, 0, 0, $mes, $dia, $anio)) - 1;
    }
}