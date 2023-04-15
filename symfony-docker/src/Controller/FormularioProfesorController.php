<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AsignaturaRepository;

class FormularioProfesorController extends AbstractController
{
    
    private AsignaturaRepository $asignaturaRepository;

    public function __construct(AsignaturaRepository $asignaturaRepository){
        $this->asignaturaRepository = $asignaturaRepository;
    }

    #[Route('/formulario/docente', name: 'app_formulario_profesor')]
    public function index(): Response
    {
        $asignaturas = $this->asignaturaRepository->findAll();

        $titulosAsignaturas = array_map(function ($asignatura) {
            return $asignatura->getNombre();
        }, $asignaturas);

        return $this->render('formularios/profesor.html.twig', [
            'controller_name' => 'FormularioProfesorController',
            'asignaturas' => $titulosAsignaturas
        ]);
    }
}
