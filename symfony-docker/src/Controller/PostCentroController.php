<?php

namespace App\Controller;

use App\Service\CentroService;
use App\Service\CalendarioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostCentroController extends AbstractController
{
    private CentroService $centroService;
    private CalendarioService $calendarioService;

    public function __construct(
        CentroService $centroService,
        CalendarioService $calendarioService
    )
    {
        $this->centroService = $centroService;
        $this->calendarioService = $calendarioService;
    }

    #[Route('/post/centro', name: 'app_post_centro')]
    public function index(): Response
    {

        //Creamos el calendario y lo obtenemos
        $calendario = $this->calendarioService->getCalendario();
        //Creamos el centro y agregamos el calendario
        $this->centroService->getCentro($calendario);

        return $this->render('posts/centro.html.twig', [
            'controller_name' => 'PostCentroController',
        ]);
    }
}
