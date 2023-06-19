<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Controller\CalendarioController;
use App\Repository\CalendarioRepository;
use App\Repository\UsuarioRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class EliminarCalendarioController extends AbstractController
{
    private CalendarioController $calendarioController;
    private UsuarioRepository $usuarioRepository;
    private CalendarioRepository $calendarioRepository;

    public function __construct(
        CalendarioController $calendarioController,
        UsuarioRepository $usuarioRepository,
        CalendarioRepository $calendarioRepository
    )
    {
        $this->calendarioController = $calendarioController;
        $this->usuarioRepository = $usuarioRepository;
        $this->calendarioRepository = $calendarioRepository;
    }

    #[Route('/eliminar/calendario', name: 'app_eliminar_calendario')]
    public function index(Request $request)
    {
        $usuarioCompleto = $request->get('usuario');
        //Asignamos el nombre y apellidos
        $nombreCompleto = explode(" ", $usuarioCompleto);
        $apellidoPr = $nombreCompleto[count($nombreCompleto) - 2];
        $apellidoSeg = $nombreCompleto[count($nombreCompleto) - 1];
        $nombre = implode(" ", array_slice($nombreCompleto, 0, count($nombreCompleto) - 2));

        //Obtenemos el usuario
        $usuario = $this->usuarioRepository->findOneByNombreApellidos($nombre, $apellidoPr, $apellidoSeg);
        $calendario = $this->calendarioRepository->findOneByUsuario($usuario->getId());
        $this->calendarioController->eliminarCalendarioCompleto($calendario);

        return $this->redirectToRoute('app_menu_profesor');
    }
}
