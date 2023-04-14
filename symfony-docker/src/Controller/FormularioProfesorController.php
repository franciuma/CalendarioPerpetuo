<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioProfesorController extends AbstractController
{
    #[Route('/formulario/docente', name: 'app_formulario_profesor')]
    public function index(): Response
    {
        return $this->render('formularios/profesor.html.twig', [
            'controller_name' => 'FormularioProfesorController',
        ]);
    }
}
