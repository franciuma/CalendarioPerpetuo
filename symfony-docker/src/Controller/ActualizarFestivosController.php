<?php

namespace App\Controller;

use App\Repository\CentroRepository;
use App\Service\FestivoCentroService;
use App\Service\FestivoLocalService;
use App\Service\FestivoNacionalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActualizarFestivosController extends AbstractController
{
    private CentroRepository $centroRepository;
    private FestivoNacionalService $festivoNacionalService;
    private FestivoLocalService $festivoLocalService;
    private FestivoCentroService $festivoCentroService;

    public function __construct(
        CentroRepository $centroRepository,
        FestivoNacionalService $festivoNacionalService,
        FestivoLocalService $festivoLocalService,
        FestivoCentroService $festivoCentroService
    )
    {
        $this->centroRepository = $centroRepository;
        $this->festivoNacionalService = $festivoNacionalService;
        $this->festivoLocalService = $festivoLocalService;
        $this->festivoCentroService = $festivoCentroService;
    }
    #[Route('/actualizar/festivos/nacionales', name: 'app_actualizar_festivos_nacionales_admin')]
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $mensaje = "Periodos no lectivos actualizados";
            $curso = $request->get('cursoacademico');
            $cursoFormato = explode("/",$curso);

            $this->festivoNacionalService->getFestivosNacionales([$cursoFormato[0], $cursoFormato[1]]);
            return $this->redirectToRoute('app_menu_periodos_nacionales_admin', ['mensaje' => $mensaje]);
        }
        return $this->render('editar/actualizarfestivosnacionales.html.twig', [
        ]);
    }

    #[Route('/actualizar/festivos/locales', name: 'app_actualizar_festivos_locales_admin')]
    public function actualizarLocales(Request $request): Response
    {
        $provincias = $this->festivoLocalService->getProvincias();
        if ($request->isMethod('POST')) {
            $mensaje = "Periodos no lectivos actualizados";
            $curso = $request->get('cursoacademico');
            $cursoFormato = explode("/",$curso);
            $provincia = $request->get('provincia');
            $this->festivoLocalService->getFestivosLocales($provincia, [$cursoFormato[0], $cursoFormato[1]]);
            return $this->redirectToRoute('app_menu_periodos_locales_admin', ['mensaje' => $mensaje]);
        }
        return $this->render('editar/actualizarfestivoslocales.html.twig', [
            'provincias' => $provincias
        ]);
    }

    #[Route('/actualizar/festivos/centro', name: 'app_actualizar_festivos_centro_admin')]
    public function actualizarCentros(Request $request): Response
    {
        $centros = $this->festivoCentroService->getNombreCentroProvincia();
        if ($request->isMethod('POST')) {
            $mensaje = "Periodos no lectivos actualizados";
            $curso = $request->get('cursoacademico');
            $cursoFormato = explode("/",$curso);
            $centro = $request->get('centro');
            $centroFormato = explode("-",$centro);
            $centroObjeto = $this->centroRepository->findOneByProvinciaCentro($centroFormato[1], $centroFormato[0]);
            $this->festivoCentroService->getFestivosCentro($centroObjeto, [$cursoFormato[0], $cursoFormato[1]]);
            return $this->redirectToRoute('app_menu_periodos_centro_admin', ['mensaje' => $mensaje]);
        }
        return $this->render('editar/actualizarfestivoscentro.html.twig', [
            'centros' => $centros
        ]);
    }
}
