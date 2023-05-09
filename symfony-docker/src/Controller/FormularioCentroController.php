<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FestivoCentroService;
use App\Service\FestivoLocalService;
use App\Repository\ProfesorRepository;

class FormularioCentroController extends AbstractController
{
    private FestivoCentroService $festivoCentroService;
    private FestivoLocalService $festivoLocalService;
    private ProfesorRepository $profesorRepository;

    public function __construct(
        FestivoCentroService $festivoCentroService,
        FestivoLocalService $festivoLocalService,
        ProfesorRepository $profesorRepository
    )
    {
        $this->festivoCentroService = $festivoCentroService;
        $this->festivoLocalService = $festivoLocalService;
        $this->profesorRepository = $profesorRepository;
    }

    #[Route('/formulario/centro', name: 'app_formulario_centro')]
    public function index(): Response
    {
        $nombreCentros = $this->festivoCentroService->getNombreCentros();
        $provincias = $this->festivoLocalService->getProvincias();
        $profesores = $this->profesorRepository->findAll();

        $nombreProfesores = array_map(function($profesor) {
            $nombre = $profesor->getNombre();
            $apellidop = $profesor->getPrimerApellido();
            $apellidos = $profesor->getSegundoApellido();
            return $nombre." ".$apellidop." ".$apellidos;
        }, $profesores);

        return $this->render('formularios/centro.html.twig', [
            'controller_name' => 'FormularioCentroController',
            'nombreCentros' => $nombreCentros,
            'provincias' => $provincias,
            'profesores' => $nombreProfesores
        ]);
    }
}
