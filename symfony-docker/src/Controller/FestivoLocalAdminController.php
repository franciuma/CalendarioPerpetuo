<?php

namespace App\Controller;

use App\Service\FestivoLocalService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FestivoLocalAdminController extends AbstractController
{
    private $provinciaSeleccionada;
    private FestivoLocalService $festivoLocalService;

    public function __construct(FestivoLocalService $festivoLocalService)
    {  
        $this->festivoLocalService = $festivoLocalService;
    }

    #[Route('/aniadir/festivo/local/admin', name: 'app_aniadir_festivo_local_admin')]
    public function index(Request $request): Response
    {
        $verFestivosDisponible = "disabled";
        if ($request->isMethod('POST')) {
            $this->provinciaSeleccionada = $request->request->get('FestivoLocalSeleccionado');
            $verFestivosDisponible = "enabled";
        }

        $festivosLocalSeleccionado = "";
        //Cogemos los festivos del centro
        if($this->provinciaSeleccionada != "") {
            $festivosLocalSeleccionado = $this->festivoLocalService->getFestivosDeProvinciaSeleccionada($this->provinciaSeleccionada);
        }

        if(empty($festivosLocalSeleccionado)) {
            $festivosLocalSeleccionado = ["La localidad no tiene festivos asociados"];
        }

        $provincias = $this->festivoLocalService->getProvincias();
        return $this->render('crear/festivolocal.html.twig', [
            'provincias' => $provincias,
            'provinciaSeleccionada' => $this->provinciaSeleccionada,
            'disponible' => $verFestivosDisponible,
            'festivosLocalSeleccionado' => $festivosLocalSeleccionado
        ]);
    }
}
