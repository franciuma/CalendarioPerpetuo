<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioAsignaturaController extends AbstractController
{
    #[Route('/formulario/asignatura', name: 'app_formulario_asignatura')]
    public function index(): Response
    {
        return $this->render('formularios/asignatura.html.twig', [
            'controller_name' => 'FormularioAsignaturaController',
        ]);
    }
}
