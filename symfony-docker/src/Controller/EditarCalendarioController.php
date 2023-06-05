<?php

namespace App\Controller;

use App\Repository\CentroRepository;
use App\Repository\UsuarioRepository;
use App\Service\UsuarioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EditarCalendarioController extends AbstractController
{
    private UsuarioRepository $usuarioRepository;
    private UsuarioService $usuarioService;
    private CentroRepository $centroRepository;

    public function __construct(
        UsuarioRepository $usuarioRepository,
        CentroRepository $centroRepository,
        UsuarioService $usuarioService
    )
    {
        $this->usuarioRepository = $usuarioRepository;
        $this->centroRepository = $centroRepository;
        $this->usuarioService = $usuarioService;
    }

    #[Route('/editar/calendario', name: 'app_editar_calendario')]
    public function index(): Response
    {
        //Filtramos los profesores que tengan un calendario creado.
        $conCalendario = true;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        return $this->render('editar/calendario.html.twig', [
            'controller_name' => 'EditarCalendarioController',
            'profesores' => $profesores
        ]);
    }

    #[Route('/obtener/info/profesor', name: 'app_info_profesor')]
    public function ObtenerInfoProfesor(Request $request): Response
    {
        //Obtenemos el profesor desde AJAX.
        $profesorNombre = $request->get('profesor');
        $nombreCompleto = explode(" ", $profesorNombre);
        //Asignamos el nombre y apellidos
        $nombre = $nombreCompleto[0];
        $apellidoPr = $nombreCompleto[1];
        $apellidoSeg = $nombreCompleto[2];

        $profesor = $this->usuarioRepository->findOneByNombreApellidos($nombre, $apellidoPr, $apellidoSeg);

        //Calculamos el centro y provincia del profesor
        $centroProfesor = $this->centroRepository->findOneByUsuario($profesor->getId());
        $provinciaProfesor = $centroProfesor->getProvincia();
        $nombreCentroProfesor = $centroProfesor->getNombre();
        // Contenido de la respuesta
        $respuesta = $nombreCentroProfesor."-".$provinciaProfesor;
        return new Response($respuesta);
    }
}
