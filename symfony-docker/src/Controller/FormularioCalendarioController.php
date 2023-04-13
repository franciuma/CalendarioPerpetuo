<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioCalendarioController extends AbstractController
{
    #[Route('/formulario/calendario', name: 'app_formulario')]
    public function index(): Response
    {
        return $this->render('formularios/calendario.html.twig', [
            'controller_name' => 'FormularioCalendarioController',
        ]);
    }
}
