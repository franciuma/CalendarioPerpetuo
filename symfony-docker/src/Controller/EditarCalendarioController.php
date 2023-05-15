<?php

namespace App\Controller;

use App\Repository\UsuarioRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditarCalendarioController extends AbstractController
{
    private UsuarioRepository $usuarioRepository;

    public function __construct(
        UsuarioRepository $usuarioRepository,
    )
    {
        $this->usuarioRepository = $usuarioRepository;
    }

    #[Route('/editar/calendario', name: 'app_editar_calendario')]
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

        return $this->render('editar/calendario.html.twig', [
            'controller_name' => 'EditarCalendarioController',
            'profesores' => $nombreProfesores
        ]);
    }
}
