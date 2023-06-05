<?php

namespace App\Controller;

use App\Service\UsuarioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrasladarCalendarioController extends AbstractController
{
    private UsuarioService $usuarioService;

    public function __construct(
        UsuarioService $usuarioService,
    ) {
        $this->usuarioService = $usuarioService;
    }

    #[Route('/trasladar', name: 'app_trasladar')]
    public function trasladarCalendario(): Response
    {
        //Filtramos los profesores que tengan un calendario creado.
        $conCalendario = true;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        return $this->render('formularios/trasladarcalendario.html.twig', [
            'profesores' => $profesores
        ]);
    }
}