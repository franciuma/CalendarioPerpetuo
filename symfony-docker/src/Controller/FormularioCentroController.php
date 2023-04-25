<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioCentroController extends AbstractController
{
    #[Route('/formulario/centro', name: 'app_formulario_centro')]
    public function index(): Response
    {
        return $this->render('formularios/centro.html.twig', [
            'controller_name' => 'FormularioCentroController',
        ]);
    }
}
