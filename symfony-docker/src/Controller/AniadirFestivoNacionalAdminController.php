<?php

namespace App\Controller;

use App\Service\FestivoNacionalService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AniadirFestivoNacionalAdminController extends AbstractController
{
    private FestivoNacionalService $festivoNacionalService;

    public function __construct(FestivoNacionalService $festivoNacionalService)
    {  
        $this->festivoNacionalService = $festivoNacionalService;
    }

    #[Route('/aniadir/festivo/nacional', name: 'app_aniadir_festivo_nacional_admin')]
    public function index(): Response
    {
        $festivosNacionales = $this->festivoNacionalService->getFestivosNacionales(self::calcularAnios());
        return $this->render('crear/festivonacional.html.twig', [
            'controller_name' => 'AniadirFestivoNacionalAdminController',
            'festivosNacionales' => $festivosNacionales
        ]);
    }

    /**
     *  Calcula los años actual y anterior en base a los meses actuales.
     *  Siempre que se cree un calendario, este será para el año actual y el siguiente.
     */
    public function calcularAnios(): array
    {
        $fechaHoy = new DateTime();
        $aniofechaHoy = $fechaHoy->format('Y');

        $anioAc = $aniofechaHoy;
        $anioSig = intval($aniofechaHoy) + 1;

        return [$anioAc, $anioSig];
    }
}
