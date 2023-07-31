<?php

namespace App\Controller;

use App\Repository\CentroRepository;
use App\Repository\FestivoCentroRepository;
use App\Service\FestivoCentroService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class FestivoCentroAdminController extends AbstractController
{
    private FestivoCentroService $festivoCentroService;
    private FestivoCentroRepository $festivoCentroRepository;
    private ManejarPostsController $manejarPostsController;
    private CentroRepository $centroRepository;
    private $centroSeleccionado = "";

    public function __construct(
        CentroRepository $centroRepository,
        FestivoCentroService $festivoCentroService,
        ManejarPostsController $manejarPostsController,
        FestivoCentroRepository $festivoCentroRepository
    )
    {  
        $this->festivoCentroService = $festivoCentroService;
        $this->manejarPostsController = $manejarPostsController;
        $this->festivoCentroRepository = $festivoCentroRepository;
        $this->centroRepository = $centroRepository;
    }

    #[Route('/aniadir/festivo/centro', name: 'app_aniadir_festivo_centro_admin')]
    #[Route('/seleccionar/editar/festivo/centro', name: 'app_seleccionar_editar_festivo_centro_admin')]
    #[Route('/seleccionar/eliminar/festivo/centro', name: 'app_seleccionar_eliminar_festivo_centro_admin')]
    public function index(Request $request): Response
    {
        $url = $request->getPathInfo();
        if($url == '/seleccionar/editar/festivo/centro'){
            $accion = "Editar festivo de centro";
            $controlador = "app_seleccionar_editar_festivo_centro_admin";
        } else if($url == '/aniadir/festivo/centro'){
            $accion = "Añadir festivo de centro";
            $controlador = "app_aniadir_festivo_centro_admin";
        } else {
            $accion = "Eliminar festivo de centro";
            $controlador = "app_seleccionar_eliminar_festivo_centro_admin";
        }

        $verFestivosDisponible = "disabled";
        if ($request->isMethod('POST')) {
            $accionPost = $request->request->get('accionPost');
            $centro = $request->request->get('centroFestivoSeleccionado');
            if($accionPost == "Eliminar festivo de centro") {
                $festivoCentro = $request->request->get('PeriodoCentro');
                return $this->redirectToRoute('app_eliminar_festivo_centro_admin',['PeriodoCentro' => $festivoCentro, 'Centro' => $centro]);
            } else if($accionPost == "Editar festivo de centro") {
                $festivoCentro = $request->request->get('PeriodoCentro');
                return $this->redirectToRoute('app_editar_festivo_centro_admin',['PeriodoCentro' => $festivoCentro, 'Centro' => $centro]);
            } else {
                $this->centroSeleccionado = $request->request->get('centroFestivoSeleccionado');
                $verFestivosDisponible = "enabled";
            }
        }

        $festivosCentroSeleccionado = "";
        //Cogemos los festivos del centro
        if($this->centroSeleccionado != "" && $this->centroSeleccionado != "-- Selecciona el centro --") {
            $festivosCentroSeleccionado = $this->festivoCentroService->getFestivosDeCentroSeleccionado($this->centroSeleccionado);
        }

        if(empty($festivosCentroSeleccionado)) {
            $festivosCentroSeleccionado = ["No tiene festivos asociados"];
        }

        $festivosCentro = $this->festivoCentroService->getNombreCentroProvincia();

        if($accion == "Añadir festivo de centro") {
            return $this->render('crear/festivocentro.html.twig', [
                'accion' => $accion,
                'controlador' => $controlador,
                'festivosCentro' => $festivosCentro,
                'centroSeleccionado' => $this->centroSeleccionado,
                'disponible' => $verFestivosDisponible,
                'festivosCentroSeleccionado' => $festivosCentroSeleccionado
            ]);
        } else {
            return $this->render('leer/periodoCentro.html.twig', [
                'accion' => $accion,
                'controlador' => $controlador,
                'festivosCentro' => $festivosCentro,
                'centroSeleccionado' => $this->centroSeleccionado,
                'disponible' => $verFestivosDisponible,
                'festivosCentroSeleccionado' => $festivosCentroSeleccionado
            ]);
        }
    }

    #[Route('/editar/festivo/centro', name: 'app_editar_festivo_centro_admin')]
    public function editar(Request $request): Response
    {
        $festivoSeleccionado = $request->query->get('PeriodoCentro');
        $centro = $request->query->get('Centro');
        $centroFormato = explode("-",$centro);
        $centroObjeto = $this->centroRepository->findOneByProvinciaCentro($centroFormato[1], $centroFormato[0]);
        $festivosFiltrados = $this->festivoCentroService->filtrarFestivos(self::calcularAnios(), $centroObjeto->getId());
        $festivoCentro = $this->festivoCentroService->buscarPorNombre($festivosFiltrados, $festivoSeleccionado, $centro);

        $festivoCentroArray = [
            'id' => $festivoCentro->getId(),
            'nombre' => $festivoCentro->getNombre(),
            'inicio' => $festivoCentro->getInicio(),
            'final' => $festivoCentro->getFinal(),
            'centro' => $festivoCentro->getCentro()->getNombre()."-".$festivoCentro->getCentro()->getProvincia()
        ];

        $festivoCentroJson = json_encode($festivoCentroArray);

        return $this->render('editar/periodoCentro.html.twig', [
            'festivoCentroJson' => $festivoCentroJson
        ]);
    }

    #[Route('/post/editar/festivo/centro', name: 'app_post_editar_festivo_centro_admin')]
    public function postEditar(Request $request): Response
    {
        $mensaje = "Periodo no lectivo de centro editado correctamente";
        $festivoCentroJson = $request->request->get('festivoscentroJSON');      
        $centro = $request->request->get('nombreCentro');      
        $festivoCentroArray = json_decode($festivoCentroJson, true);

        $this->manejarPostsController->editarPosts("festivosCentro", $festivoCentroArray, $centro);

        $festivoCentro = $this->festivoCentroRepository->find($festivoCentroArray[0]['id']);

        $this->festivoCentroService->editaFestivoCentro(self::calcularAnios(), $festivoCentro, $festivoCentroArray);

        $this->festivoCentroRepository->flush();

        return $this->redirectToRoute('app_menu_periodos_centro_admin',["mensaje" => $mensaje]);
    }

    #[Route('/eliminar/festivo/centro', name: 'app_eliminar_festivo_centro_admin')]
    public function eliminar(Request $request): Response
    {
        $mensaje = "Periodo de centro eliminado correctamente";
        $festivoSeleccionado = $request->query->get('PeriodoCentro');
        $centro = $request->query->get('Centro');
        $this->festivoCentroService->eliminarFestivoCompleto($festivoSeleccionado, $centro);

        return $this->redirectToRoute('app_menu_periodos_centro_admin',["mensaje" => $mensaje]);
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
