<?php

namespace App\Controller;

use App\Service\TitulacionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostTitulacionController extends AbstractController
{
    private TitulacionService $titulacionService;

    public function __construct(TitulacionService $titulacionService)
    {
        $this->titulacionService = $titulacionService;
    }

    #[Route('/post/titulacion', name: 'app_post_titulacion')]
    public function index(): Response
    {
        //Persistimos las titulaciones
        $this->titulacionService->getTitulaciones();
        return $this->render('posts/titulacion.html.twig', [
            'controller_name' => 'PostTitulacionController',
        ]);
    }
}
