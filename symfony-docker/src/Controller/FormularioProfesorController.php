<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioProfesorController extends AbstractController
{
    #[Route('/formulario/profesor', name: 'app_formulario_profesor')]
    public function index(): Response
    {
        return $this->render('formulario/formularioProfesor.html.twig', [
            'controller_name' => 'FormularioProfesorController',
        ]);
    }
}
