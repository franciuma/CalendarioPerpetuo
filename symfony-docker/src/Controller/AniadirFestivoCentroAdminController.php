<?php

namespace App\Controller;

use App\Service\FestivoCentroService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AniadirFestivoCentroAdminController extends AbstractController
{
    private FestivoCentroService $festivoCentroService;
    private $centroSeleccionado = "";

    public function __construct(FestivoCentroService $festivoCentroService)
    {  
        $this->festivoCentroService = $festivoCentroService;
    }

    #[Route('/aniadir/festivo/centro', name: 'app_aniadir_festivo_centro_admin')]
    public function index(Request $request): Response
    {
        $verFestivosDisponible = "disabled";
        if ($request->isMethod('POST')) {
            $this->centroSeleccionado = $request->request->get('centroFestivoSeleccionado');
            $verFestivosDisponible = "enabled";
        }

        $festivosCentroSeleccionado = "";
        //Cogemos los festivos del centro
        if($this->centroSeleccionado != "") {
            $festivosCentroSeleccionado = $this->festivoCentroService->getFestivosDeCentroSeleccionado($this->centroSeleccionado);
        }

        if(empty($festivosCentroSeleccionado)) {
            $festivosCentroSeleccionado = ["No tiene festivos asociados"];
        }

        $festivosCentro = $this->festivoCentroService->getNombreCentroProvincia();
        return $this->render('festivo_centro/aniadir.html.twig', [
            'controller_name' => 'AniadirFestivoCentroAdminController',
            'festivosCentro' => $festivosCentro,
            'centroSeleccionado' => $this->centroSeleccionado,
            'disponible' => $verFestivosDisponible,
            'festivosCentroSeleccionado' => $festivosCentroSeleccionado
        ]);
    }
}
