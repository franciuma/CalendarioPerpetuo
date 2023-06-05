<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UsuarioService;

class LeerCalendarioController extends AbstractController
{
    private UsuarioService $usuarioService;

    public function __construct(
        UsuarioService $usuarioService,
    )
    {
        $this->usuarioService = $usuarioService;
    }

    #[Route('/leer/calendario', name: 'app_leer_calendario')]
    public function index(): Response
    {
        //Filtramos los profesores que tengan un calendario creado.
        $conCalendario = true;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        return $this->render('leer/calendario.html.twig', [
            'controller_name' => 'LeerCalendarioController',
            'profesores' => $profesores
        ]);
    }
}
