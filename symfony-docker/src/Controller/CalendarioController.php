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

        for ($numMes=0; $numMes <= 5; $numMes++) {
            $mesActual = date('n')+$numMes;
            $mes = new Mes($mesActual);
            $anio->addMes($mes);
            $mes->setNombre($calendario->getMeses()[$mesActual]);
            $primerDiaDeMes = intval(self::primerDiaMes(1, $mesActual, $anio->getNombre()));
            $mes->setPrimerDia($primerDiaDeMes);
            $ultimoDiaDeMes = intval(self::ultimoDiaMes($mesActual, $anio->getNombre()));
            
            for($numDia= 1; $numDia <= $ultimoDiaDeMes; $numDia++) {
                $dia = new Dia($numDia);
                $mes->addDia($dia);
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

    public function primerDiaMes($dia, $mes, $anio) {
        return date('N', mktime(0, 0, 0, $mes, $dia, $anio)) - 1;
    }
}