<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FestivoCentroService;
use App\Service\FestivoLocalService;
use App\Repository\UsuarioRepository;

class FormularioCentroController extends AbstractController
{
    private FestivoCentroService $festivoCentroService;
    private FestivoLocalService $festivoLocalService;
    private UsuarioRepository $usuarioRepository;

    public function __construct(
        FestivoCentroService $festivoCentroService,
        FestivoLocalService $festivoLocalService,
        UsuarioRepository $usuarioRepository
    )
    {
        $this->festivoCentroService = $festivoCentroService;
        $this->festivoLocalService = $festivoLocalService;
        $this->usuarioRepository = $usuarioRepository;
    }

    #[Route('/formulario/centro', name: 'app_formulario_centro')]
    public function index(): Response
    {
        $nombreCentros = $this->festivoCentroService->getNombreCentros();
        $provincias = $this->festivoLocalService->getProvincias();
        $profesores = $this->usuarioRepository->findAllProfesores();

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
