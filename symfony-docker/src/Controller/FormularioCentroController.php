<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FestivoCentroService;
use App\Repository\UsuarioRepository;
use App\Service\CentroService;
use App\Service\FestivoLocalService;
use App\Service\FestivoNacionalService;

class FormularioCentroController extends AbstractController
{
    private CentroService $centroService;
    private UsuarioRepository $usuarioRepository;
    private FestivoCentroService $festivoCentroService;
    private FestivoNacionalService $festivoNacionalService;
    private FestivoLocalService $festivoLocalService;

    public function __construct(
        CentroService $centroService,
        UsuarioRepository $usuarioRepository,
        FestivoNacionalService $festivoNacionalService,
        FestivoLocalService $festivoLocalService,
        FestivoCentroService $festivoCentroService
    )
    {
        $this->centroService = $centroService;
        $this->usuarioRepository = $usuarioRepository;
        $this->festivoNacionalService = $festivoNacionalService;
        $this->festivoLocalService = $festivoLocalService;
        $this->festivoCentroService = $festivoCentroService;
    }

    #[Route('/formulario/centro', name: 'app_formulario_centro')]
    public function index(): Response
    {
        $nombreCentrosProvincias = $this->festivoCentroService->getNombreCentroProvincia();
        $profesores = $this->usuarioRepository->findAllProfesores();

        $nombreProfesores = array_map(function($profesor) {
            $nombre = $profesor->getNombre();
            $apellidop = $profesor->getPrimerApellido();
            $apellidos = $profesor->getSegundoApellido();
            return $nombre." ".$apellidop." ".$apellidos;
        }, $profesores);

        return $this->render('formularios/centro.html.twig', [
            'controller_name' => 'FormularioCentroController',
            'nombreCentrosProvincias' => $nombreCentrosProvincias,
            'profesores' => $nombreProfesores
        ]);
    }

    #[Route('/post/centro', name: 'app_post_centro')]
    public function postCentro(): Response
    {
        //Creamos el centro
        //$centro = $this->centroService->getCentro();
        //Creamos los festivos nacionales
        //Puede ser opcional, que se creen por defecto y ya si se añaden más se metan en administrador.
        //$this->festivoNacionalService->getFestivosNacionales();
        //Creamos los festivos locales
        //$this->festivoLocalService->getFestivosLocales($centro->getProvincia());
        //Creamos los festivos de centro
        //$this->festivoCentroService->getFestivosCentro($centro);

        //Redirigimos al controlador de previsualización de calendario
        return $this->redirectToRoute('app_formulario_calendario');
    }
}
