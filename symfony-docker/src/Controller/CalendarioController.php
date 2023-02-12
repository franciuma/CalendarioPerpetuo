<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        //FALTA INTENAR QUE EL OBJETO CALENDARIO FUNCIONE.
        $ultimoDiaDelMes = self::ultimoDiaMes($mes, $anio);
        $primerDiaDelMes = self::primerDiaMes(1, $mes, $anio);
        $meses = [1 => 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $diasSemana = [0 => 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado','Domingo'];
        $primerDiaDeCadaMes = [];
        $ultimoDiaDeCadaMes = [];

        for ($i=0; $i <= 5; $i++) { 
            array_push($primerDiaDeCadaMes, self::primerDiaMes(1, $mes+$i, $anio));
            array_push($ultimoDiaDeCadaMes, self::ultimoDiaMes($mes+$i, $anio));
        }

        return $this->render('calendario/index.html.twig', [
            'mes' => $mes,
            'ano' => $anio,
            'ultimo_dia_mes' => $ultimoDiaDelMes, //no se usa
            'dia_semana' => $primerDiaDelMes,     //no se usa
            'meses' => $meses,
            'dias_semana' => $diasSemana,
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