<?php

namespace App\Controller;

use App\Service\FestivoCentroService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AniadirFestivoCentroAdminController extends AbstractController
{
    private FestivoCentroService $festivoCentroService;

    public function __construct(FestivoCentroService $festivoCentroService)
    {  
        $this->festivoCentroService = $festivoCentroService;
    }

    #[Route('/aniadir/festivo/centro', name: 'app_aniadir_festivo_centro_admin')]
    public function index(): Response
    {
        $festivosCentro = $this->festivoCentroService->getNombreCentroProvincia();
        return $this->render('festivo_centro/aniadir.html.twig', [
            'controller_name' => 'AniadirFestivoCentroAdminController',
            'festivosCentro' => $festivosCentro
        ]);
    }
}
