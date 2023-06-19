<?php

namespace App\Controller;

use App\Repository\TitulacionRepository;
use App\Service\TitulacionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostTitulacionController extends AbstractController
{
    private TitulacionService $titulacionService;
    private TitulacionRepository $titulacionRepository;

    public function __construct(TitulacionService $titulacionService, TitulacionRepository $titulacionRepository)
    {
        $this->titulacionService = $titulacionService;
        $this->titulacionRepository = $titulacionRepository;
    }

    #[Route('/post/titulacion', name: 'app_post_titulacion')]
    #[Route('/post/titulacion/editado', name: 'app_post_titulacion_editado')]
    public function index(Request $request): Response
    {
        if(($request->getPathInfo() == '/post/titulacion')) {
            //Persistimos las titulaciones
            $this->titulacionService->getTitulaciones();
        } else {
            $titulacionId = $request->get('titulacion');
            $titulacionObjeto = $this->titulacionRepository->find($titulacionId);
            $this->titulacionService->editarTitulacion($titulacionObjeto);
        }

        return $this->render('posts/titulacion.html.twig', [
            'controller_name' => 'PostTitulacionController',
        ]);
    }
}
