<?php

namespace App\Controller;

use App\Service\CentroService;
use App\Service\CalendarioService;
use App\Service\FestivoCentroService;
use App\Service\FestivoNacionalService;
use App\Service\FestivoLocalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostCentroController extends AbstractController
{
    private CentroService $centroService;
    private FestivoNacionalService $festivoNacionalService;
    private FestivoLocalService $festivoLocalService;
    private FestivoCentroService $festivoCentroService;

    public function __construct(
        CentroService $centroService,
        FestivoNacionalService $festivoNacionalService,
        FestivoLocalService $festivoLocalService,
        FestivoCentroService $festivoCentroService
    )
    {
        $this->centroService = $centroService;
        $this->festivoNacionalService = $festivoNacionalService;
        $this->festivoLocalService = $festivoLocalService;
        $this->festivoCentroService = $festivoCentroService;
    }

    #[Route('/post/centro', name: 'app_post_centro')]
    public function index(): Response
    {
        //Creamos el centro
        $centro = $this->centroService->getCentro();
        //Creamos los festivos nacionales
        //Puede ser opcional, que se creen por defecto y ya si se añaden más se metan en administrador.
        $this->festivoNacionalService->getFestivosNacionales();
        //Creamos los festivos locales
        $this->festivoLocalService->getFestivosLocales($centro);
        //Creamos los festivos de centro
        $this->festivoCentroService->getFestivosCentro($centro);

        return $this->render('posts/centro.html.twig', [
            'controller_name' => 'PostCentroController',
        ]);
    }
}
