<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Calendario;
use App\Entity\Dia;
use App\Entity\Mes;
use App\Entity\Anio;


class CalendarioController extends AbstractController
{
    /**
     * @Route("/calendario", name="calendar")
     */
    public function index(): Response
    {
        $anio = new Anio(date('Y'));
        $calendario = new Calendario($anio->getNombre());
        $arrayMeses = [];
        $contadorDias = 1;

        for ($i=0; $i <= 5; $i++) {
            $mesActual = date('n')+$i;
            $mes = new Mes($mesActual);
            $mes->setNombre($calendario->getMeses()[$mesActual]);
            $primerDiaDeMes = intval(self::primerDiaMes(1, $mesActual, $anio->getNombre()));
            $mes->setPrimerDia($primerDiaDeMes);
            $ultimoDiaDeMes = intval(self::ultimoDiaMes($mesActual, $anio->getNombre()));
            //array_push($primerDiaDeCadaMes, self::primerDiaMes(1, $mes+$i, $anio));
            //array_push($ultimoDiaDeCadaMes, self::ultimoDiaMes($mes+$i, $anio));
            for($j= 1; $j <= $ultimoDiaDeMes; $j++) {
                $dia = new Dia($contadorDias);
                $contadorDias++;
                $mes->addDia($dia);
                //$mes[]
                //array_push($arrayMeses, $dia->getValor());
            }
            $contadorDias = 1;
            array_push($arrayMeses, $mes);
        }

        return $this->render('calendario/index.html.twig', [
            'anio' => $anio->getNombre(),
            'meses' => $calendario->getMeses(),
            'dias_semana' => $calendario->getdiasSemana(),
            //'primer_dia_de_cada_mes' => $primerDiaDeCadaMes,
            //'ultimo_dia_de_cada_mes' => $ultimoDiaDeCadaMes,
            'array_meses' => $arrayMeses,
        ]);
    }

    public function ultimoDiaMes($mes, $anio) {
        return date('d', mktime(0, 0, 0, $mes + 1, 0, $anio));
    }

    public function primerDiaMes($dia, $mes, $anio) {
        return date('N', mktime(0, 0, 0, $mes, $dia, $anio)) - 1;
    }
}