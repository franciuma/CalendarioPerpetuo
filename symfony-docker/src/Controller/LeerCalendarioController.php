<?php

namespace App\Controller;

use App\Repository\CalendarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UsuarioRepository;

class LeerCalendarioController extends AbstractController
{
    private UsuarioRepository $usuarioRepository;
    private CalendarioRepository $calendarioRepository;

    public function __construct(
        UsuarioRepository $usuarioRepository,
        CalendarioRepository $calendarioRepository
    )
    {
        $this->usuarioRepository = $usuarioRepository;
        $this->calendarioRepository = $calendarioRepository;
    }

    #[Route('/leer/calendario', name: 'app_leer_calendario')]
    public function index(): Response
    {
        //Filtramos los profesores que tengan un calendario creado.
        $profesores = $this->usuarioRepository->findAllProfesoresConCalendario();

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
