<?php

namespace App\Controller;

use App\Service\CentroService;
use App\Service\CalendarioService;
use App\Service\FestivoNacionalService;
use App\Service\FestivoLocalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostCentroController extends AbstractController
{
    private CentroService $centroService;
    private CalendarioService $calendarioService;
    private FestivoNacionalService $festivoNacionalService;
    private FestivoLocalService $festivoLocalService;

    public function __construct(
        CentroService $centroService,
        CalendarioService $calendarioService,
        FestivoNacionalService $festivoNacionalService,
        FestivoLocalService $festivoLocalService
    )
    {
        $this->centroService = $centroService;
        $this->calendarioService = $calendarioService;
        $this->festivoNacionalService = $festivoNacionalService;
        $this->festivoLocalService = $festivoLocalService;
    }

    #[Route('/post/centro', name: 'app_post_centro')]
    public function index(): Response
    {

        //Creamos el calendario y lo obtenemos
        $calendario = $this->calendarioService->getCalendario();
        //Creamos el centro y agregamos el calendario
        $centro = $this->centroService->getCentro($calendario);
        //Creamos los festivos nacionales
        $this->festivoNacionalService->getFestivosNacionales();
        //Creamos los festivos locales
        $this->festivoLocalService->getFestivosLocales($centro);

        return $this->render('posts/centro.html.twig', [
            'controller_name' => 'PostCentroController',
        ]);
    }
}