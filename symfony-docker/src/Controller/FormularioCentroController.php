<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FestivoCentroService;
use App\Service\FestivoLocalService;

class FormularioCentroController extends AbstractController
{
    private FestivoCentroService $festivoCentroService;
    private FestivoLocalService $festivoLocalService;

    public function __construct(
        FestivoCentroService $festivoCentroService,
        FestivoLocalService $festivoLocalService
    )
    {
        $this->festivoCentroService = $festivoCentroService;
        $this->festivoLocalService = $festivoLocalService;
    }

    #[Route('/formulario/centro', name: 'app_formulario_centro')]
    public function index(): Response
    {
        $nombreCentros = $this->festivoCentroService->getNombreCentros();
        $provincias = $this->festivoLocalService->getProvincias();
        return $this->render('formularios/centro.html.twig', [
            'controller_name' => 'FormularioCentroController',
            'nombreCentros' => $nombreCentros,
            'provincias' => $provincias
        ]);
    }
}
