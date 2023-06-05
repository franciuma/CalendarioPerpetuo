<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use App\Service\FestivoLocalService;
use App\Service\FestivoNacionalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrasladarCalendarioController extends AbstractController
{
    private UsuarioRepository $usuarioRepository;
    private FestivoNacionalService $festivoNacionalService;
    private FestivoLocalService $festivoLocalService;

    public function __construct(
        UsuarioRepository $usuarioRepository,
        FestivoNacionalService $festivoNacionalService,
        FestivoLocalService $festivoLocalService
    ) {
        $this->usuarioRepository = $usuarioRepository;
        $this->festivoNacionalService = $festivoNacionalService;
        $this->festivoLocalService = $festivoLocalService;
    }

    #[Route('/trasladar', name: 'app_trasladar')]
    public function trasladarCalendario(): Response
    {
        //Filtramos los profesores que tengan un calendario creado. 
        $profesores = $this->usuarioRepository->findAllProfesoresConCalendario();

        $nombreProfesores = array_map(function($profesor) {
            $nombre = $profesor->getNombre();
            $apellidop = $profesor->getPrimerApellido();
            $apellidos = $profesor->getSegundoApellido();
            return $nombre." ".$apellidop." ".$apellidos;
        }, $profesores);

        return $this->render('formularios/trasladarcalendario.html.twig', [
            'profesores' => $nombreProfesores
        ]);
    }
}