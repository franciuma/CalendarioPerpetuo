<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UsuarioService;
use Symfony\Component\HttpFoundation\Request;

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
    #[Route('/seleccionar/eliminar/calendario', name: 'app_seleccionar_eliminar_calendario')]
    public function index(Request $request): Response
    {
        //Filtramos los profesores que tengan un calendario creado.
        $conCalendario = true;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        if ($request->getPathInfo() == '/seleccionar/eliminar/calendario') {
            $accion = "Eliminar";
        } else {
            $accion = "Ver";
        }

        return $this->render('leer/calendario.html.twig', [
            'controller_name' => 'LeerCalendarioController',
            'profesores' => $profesores,
            'accion' => $accion
        ]);
    }
}
