<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioController extends AbstractController
{
    #[Route('/formulario', name: 'app_formulario')]
    public function index(): Response
    {
        return $this->render('formulario/index.html.twig', [
            'controller_name' => 'FormularioController',
        ]);
    }
}
