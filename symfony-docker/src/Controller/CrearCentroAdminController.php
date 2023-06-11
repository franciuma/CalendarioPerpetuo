<?php

namespace App\Controller;

use App\Service\FestivoCentroService;
use App\Service\FestivoLocalService;
use App\Service\CentroService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CrearCentroAdminController extends AbstractController
{
    private CentroService $centroService;
    private FestivoCentroService $festivoCentroService;
    private FestivoLocalService $festivoLocalService;

    public function __construct(
        CentroService $centroService,
        FestivoCentroService $festivoCentroService,
        FestivoLocalService $festivoLocalService
    ) {
        $this->festivoCentroService = $festivoCentroService;
        $this->festivoLocalService = $festivoLocalService;
        $this->centroService = $centroService;
    }

    #[Route('/crear/centro/admin', name: 'app_crear_centro_admin')]
    public function index(): Response
    {
        $provincias = $this->festivoLocalService->getProvincias();
        $centros = $this->festivoCentroService->getNombreCentroProvincia();

        $centrosJson = json_encode($centros);

        return $this->render('crear/centro.html.twig', [
            'controller_name' => 'CrearCentroAdminController',
            'provincias' => $provincias,
            'centros' => $centrosJson
        ]);
    }

    //Crear un nodo centro vacío (solo el título)
    #[Route('/crear/centro/admin/procesar', name: 'app_crear_centro_admin_procesar', methods: ['POST'])]
    public function procesarFormulario(Request $request)
    {
        // Obtén los datos del formulario
        $nombreCentro = $request->request->get('nombreDelCentro');
        $nombreProvincia = $request->request->get('nombreDeProvincia');

        // Crea un array con los datos del nuevo centro
        $tituloJson = "festivosCentro" . $nombreCentro . "-" . $nombreProvincia;
        $nuevoCentro = [];

        // Lee el contenido actual del archivo JSON
        $rutaArchivo = '/app/src/resources/festivosCentro.json';
        $contenidoJson = file_get_contents($rutaArchivo);

        // Decodifica el contenido JSON en un array asociativo
        $datosJson = json_decode($contenidoJson, true);

        // Agrega el nuevo centro al array existente 
        try {
            if($nombreCentro == "") {
                throw new Exception("Nombre del centro vacío");
            }
            if($nombreProvincia == "-- Selecciona el nombre de la localidad --") {
                throw new Exception("Nombre de provincia vacía");
            }
            if(self::centroExistente($nombreCentro, $nombreProvincia)){
                throw new Exception("Centro ya existente");
            }
            $datosJson[$tituloJson] = $nuevoCentro;
        } catch (Exception $e) {
            return $this->redirectToRoute('app_crear_centro_admin');
        }

        // Codifica los datos actualizados a JSON
        $contenidoActualizado = json_encode($datosJson, JSON_PRETTY_PRINT);

        // Guarda los cambios en el archivo JSON
        file_put_contents($rutaArchivo, $contenidoActualizado);

        //Creamos el centro en la base de datos
        $this->centroService->insertaCentroBd($nombreProvincia, $nombreCentro);

        // Redirecciona a la ruta 'app_menu_administrador'
        return $this->redirectToRoute('app_menu_administrador');
    }

    public function centroExistente($nombreCentro, $nombreProvincia): bool
    {
        $centros = $this->festivoCentroService->getNombreCentroProvincia();
        return in_array($nombreCentro.'-'.$nombreProvincia, $centros);
    }
}
