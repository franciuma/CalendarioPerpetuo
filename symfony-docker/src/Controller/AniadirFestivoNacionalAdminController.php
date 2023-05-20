<?php

namespace App\Controller;

use App\Service\FestivoNacionalService;
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
        $festivosNacionales = $this->festivoNacionalService->getFestivosNacionales();
        return $this->render('crear/festivonacional.html.twig', [
            'controller_name' => 'AniadirFestivoNacionalAdminController',
            'festivosNacionales' => $festivosNacionales
        ]);
    }
}
