<?php

namespace App\Controller;

use App\Repository\FestivoLocalRepository;
use App\Service\FestivoLocalService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FestivoLocalAdminController extends AbstractController
{
    private $provinciaSeleccionada;
    private FestivoLocalService $festivoLocalService;
    private ManejarPostsController $manejarPostsController;
    private FestivoLocalRepository $festivoLocalRepository;

    public function __construct(FestivoLocalService $festivoLocalService, ManejarPostsController $manejarPostsController, FestivoLocalRepository $festivoLocalRepository)
    {  
        $this->festivoLocalService = $festivoLocalService;
        $this->manejarPostsController = $manejarPostsController;
        $this->festivoLocalRepository = $festivoLocalRepository;
    }

    #[Route('/aniadir/festivo/local/admin', name: 'app_aniadir_festivo_local_admin')]
    #[Route('/seleccionar/editar/festivo/local', name: 'app_seleccionar_editar_festivo_local_admin')]
    #[Route('/seleccionar/eliminar/festivo/local', name: 'app_seleccionar_eliminar_festivo_local_admin')]
    public function index(Request $request): Response
    {
        $url = $request->getPathInfo();
        if($url == '/seleccionar/editar/festivo/local'){
            $accion = "Editar festivo local";
            $controlador = "app_seleccionar_editar_festivo_local_admin";
        } else if($url == '/aniadir/festivo/local/admin'){
            $accion = "Añadir festivo local";
            $controlador = "app_aniadir_festivo_local_admin";
        } else {
            $accion = "Eliminar festivo local";
            $controlador = "app_seleccionar_eliminar_festivo_local_admin";
        }

        $verFestivosDisponible = "disabled";
        if ($request->isMethod('POST')) {
            $accionPost = $request->request->get('accionPost');
            $provincia = $request->request->get('FestivoLocalSeleccionado');
            if($accionPost == "Eliminar festivo local") {
                $festivoLocal = $request->request->get('PeriodoLocal');
                return $this->redirectToRoute('app_eliminar_festivo_local_admin',['PeriodoLocal' => $festivoLocal, 'Provincia' => $provincia]);
            } else if($accionPost == "Editar festivo local") {
                $festivoLocal = $request->request->get('PeriodoLocal');
                return $this->redirectToRoute('app_editar_festivo_local_admin',['PeriodoLocal' => $festivoLocal, 'Provincia' => $provincia]);
            } else {
                $this->provinciaSeleccionada = $request->request->get('FestivoLocalSeleccionado');
                $verFestivosDisponible = "enabled";
            }
        }

        $festivosLocalSeleccionado = "";
        //Cogemos los festivos del centro
        if($this->provinciaSeleccionada != "" && $this->provinciaSeleccionada != "-- Selecciona la localidad --") {
            $festivosLocalSeleccionado = $this->festivoLocalService->getFestivosDeProvinciaSeleccionada($this->provinciaSeleccionada);
        }

        if(empty($festivosLocalSeleccionado)) {
            $festivosLocalSeleccionado = ["La localidad no tiene festivos asociados"];
        }

        $provincias = $this->festivoLocalService->getProvincias();
        if($accion == "Añadir festivo local") {
            return $this->render('crear/festivolocal.html.twig', [
                'provincias' => $provincias,
                'provinciaSeleccionada' => $this->provinciaSeleccionada,
                'disponible' => $verFestivosDisponible,
                'festivosLocalSeleccionado' => $festivosLocalSeleccionado
            ]);
        } else {
            return $this->render('leer/periodoLocal.html.twig', [
                'accion' => $accion,
                'controlador' => $controlador,
                'provincias' => $provincias,
                'provinciaSeleccionada' => $this->provinciaSeleccionada,
                'disponible' => $verFestivosDisponible,
                'festivosLocalSeleccionado' => $festivosLocalSeleccionado
            ]);
        }
    }

    #[Route('/editar/festivo/local', name: 'app_editar_festivo_local_admin')]
    public function editar(Request $request): Response
    {
        $festivoSeleccionado = $request->query->get('PeriodoLocal');
        $provincia = $request->query->get('Provincia');
        $festivosFiltrados = $this->festivoLocalService->filtrarFestivos(self::calcularAnios());
        $festivoLocal = $this->festivoLocalService->buscarPorNombre($festivosFiltrados, $festivoSeleccionado, $provincia);

        $festivoLocalArray = [
            'id' => $festivoLocal->getId(),
            'nombre' => $festivoLocal->getNombre(),
            'inicio' => $festivoLocal->getInicio(),
            'final' => $festivoLocal->getFinal(),
            'provincia' => $festivoLocal->getProvincia()
        ];

        $festivoLocalJson = json_encode($festivoLocalArray);

        return $this->render('editar/periodoLocal.html.twig', [
            'festivoLocalJson' => $festivoLocalJson
        ]);
    }

    #[Route('/post/editar/festivo/local', name: 'app_post_editar_festivo_local_admin')]
    public function postEditar(Request $request): Response
    {
        $mensaje = "Periodo no lectivo local editado correctamente";
        $festivoLocalJson = $request->request->get('festivoslocalesJSON');      
        $provincia = $request->request->get('provincia');      
        $festivoLocalArray = json_decode($festivoLocalJson, true);

        $this->manejarPostsController->editarPosts("festivosLocales", $festivoLocalArray, $provincia);

        $festivoLocal = $this->festivoLocalRepository->find($festivoLocalArray[0]['id']);
        
        $this->festivoLocalService->editaFestivoLocal(self::calcularAnios(), $festivoLocal, $festivoLocalArray);

        $this->festivoLocalRepository->flush();

        return $this->redirectToRoute('app_menu_periodos_nacionales_admin',["mensaje" => $mensaje]);
    }

    #[Route('/eliminar/festivo/local', name: 'app_eliminar_festivo_local_admin')]
    public function eliminar(Request $request): Response
    {
        $mensaje = "Periodo local eliminado correctamente";
        $festivoSeleccionado = $request->query->get('PeriodoLocal');
        $provincia = $request->query->get('Provincia');
        $this->festivoLocalService->eliminarFestivoCompleto($festivoSeleccionado, $provincia);

        return $this->redirectToRoute('app_menu_periodos_locales_admin',["mensaje" => $mensaje]);
    }

    /**
     *  Calcula los años actual y anterior en base a los meses actuales.
     *  Siempre que se cree un calendario, este será para el año actual y el siguiente.
     */
    public function calcularAnios(): array
    {
        $fechaHoy = new DateTime();
        $aniofechaHoy = $fechaHoy->format('Y');

        $anioAc = $aniofechaHoy;
        $anioSig = intval($aniofechaHoy) + 1;

        return [$anioAc, $anioSig];
    }
}
