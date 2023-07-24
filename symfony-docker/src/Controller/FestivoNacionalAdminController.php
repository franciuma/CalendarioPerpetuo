<?php

namespace App\Controller;

use App\Repository\FestivoNacionalRepository;
use App\Service\FestivoNacionalService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FestivoNacionalAdminController extends AbstractController
{
    private FestivoNacionalService $festivoNacionalService;
    private FestivoNacionalRepository $festivoNacionalRepository;
    private ManejarPostsController $manejarPostsController;

    public function __construct(
        FestivoNacionalService $festivoNacionalService,
        FestivoNacionalRepository $festivoNacionalRepository,
        ManejarPostsController $manejarPostsController
    )
    {  
        $this->festivoNacionalService = $festivoNacionalService;
        $this->festivoNacionalRepository = $festivoNacionalRepository;
        $this->manejarPostsController = $manejarPostsController;
    }

    #[Route('/aniadir/festivo/nacional', name: 'app_aniadir_festivo_nacional_admin')]
    public function index(): Response
    {
        $festivosNacionales = $this->festivoNacionalService->getFestivosNacionales(self::calcularAnios(), false);
        return $this->render('crear/festivonacional.html.twig', [
            'festivosNacionales' => $festivosNacionales
        ]);
    }

    #[Route('/seleccionar/editar/festivo/nacional', name: 'app_seleccionar_editar_festivo_nacional_admin')]
    #[Route('/seleccionar/eliminar/festivo/nacional', name: 'app_seleccionar_eliminar_festivo_nacional_admin')]
    public function seleccionar(Request $request): Response
    {
        $url = $request->getPathInfo();
        if($url == '/seleccionar/editar/festivo/nacional'){
            $accion = "Editar festivo nacional";
            $controlador = "app_seleccionar_editar_festivo_nacional_admin";
        } else if($url == '/seleccionar/eliminar/festivo/nacional') {
            $accion = "Eliminar festivo nacional";
            $controlador = "app_seleccionar_eliminar_festivo_nacional_admin";
        }

        if ($request->isMethod('POST')) {
            $festivoSeleccionado = $request->get("periodoNacionalSeleccionado");

            if($accion == "Editar festivo nacional") {
                return $this->redirectToRoute('app_editar_festivo_nacional_admin',["festivoSeleccionado" => $festivoSeleccionado]);
            } else if ($accion == "Eliminar festivo nacional") {
                return $this->redirectToRoute('app_eliminar_festivo_nacional_admin',["festivoSeleccionado" => $festivoSeleccionado]);
            }
        }

        $festivosNacionales = $this->festivoNacionalService->getFestivosNacionales(self::calcularAnios(), false);
        return $this->render('leer/periodoNacional.html.twig', [
            'festivosNacionales' => $festivosNacionales,
            'accion' => $accion,
            'controlador' => $controlador
        ]);
    }

    #[Route('/editar/festivo/nacional', name: 'app_editar_festivo_nacional_admin')]
    public function editar(Request $request): Response
    {
        $festivoSeleccionado = $request->get("festivoSeleccionado");
        $festivosFiltrados = $this->festivoNacionalService->filtrarFestivos(self::calcularAnios());
        $festivoNacional = $this->festivoNacionalService->buscarPorNombre($festivosFiltrados, $festivoSeleccionado);

        $festivoNacionalArray = [
            'id' => $festivoNacional->getId(),
            'nombre' => $festivoNacional->getNombre(),
            'inicio' => $festivoNacional->getInicio(),
            'final' => $festivoNacional->getFinal(),
        ];

        $festivoNacionalJson = json_encode($festivoNacionalArray);

        return $this->render('editar/periodoNacional.html.twig', [
            'festivoNacionalJson' => $festivoNacionalJson,
        ]);
    }

    #[Route('/post/editar/festivo/nacional', name: 'app_post_editar_festivo_nacional_admin')]
    public function postEditar(Request $request): Response
    {
        $mensaje = "Periodo no lectivo nacional editado correctamente";
        $festivoNacionalJson = $request->request->get('festivosnacionalesJSON');
        $festivoNacionalArray = json_decode($festivoNacionalJson, true);

        //Lo incluimos en el json
        $this->manejarPostsController->editarPosts("festivosNacionales", $festivoNacionalArray);

        $festivoNacional = $this->festivoNacionalRepository->find($festivoNacionalArray[0]['id']);
        
        $this->festivoNacionalService->editaFestivoNacional(self::calcularAnios(), $festivoNacional, $festivoNacionalArray);

        $this->festivoNacionalRepository->flush();

        return $this->redirectToRoute('app_menu_periodos_nacionales_admin',["mensaje" => $mensaje]);
    }

    #[Route('/eliminar/festivo/nacional', name: 'app_eliminar_festivo_nacional_admin')]
    public function eliminar(Request $request): Response
    {
        $festivoSeleccionado = $request->get("festivoSeleccionado");

        return $this->render('editar/periodoNacional.html.twig', [
            'festivoSeleccionado' => $festivoSeleccionado,
        ]);
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
