<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsuarioRepository;

class LeerCalendarioController extends AbstractController
{
    private UsuarioRepository $usuarioRepository;

    public function __construct(
        UsuarioRepository $usuarioRepository
    )
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    #[Route('/leer/calendario', name: 'app_leer_calendario')]
    public function index(): Response
    {
        $profesores = $this->usuarioRepository->findAllProfesores();
        //Meter $this->claseRepository->findOneByCalendario($calendario->getId())
        $nombreProfesores = array_map(function($profesor) {
            $nombre = $profesor->getNombre();
            $apellidop = $profesor->getPrimerApellido();
            $apellidos = $profesor->getSegundoApellido();
            return $nombre." ".$apellidop." ".$apellidos;
        }, $profesores);

        return $this->render('leer/calendario.html.twig', [
            'controller_name' => 'LeerCalendarioController',
            'profesores' => $nombreProfesores
        ]);
    }
}
