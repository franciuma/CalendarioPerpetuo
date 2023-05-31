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

        return $this->render('crear/centro.html.twig', [
            'controller_name' => 'CrearCentroAdminController',
            'provincias' => $provincias
        ]);
    }

    //Crear un nodo centro vacío (solo el título)
    #[Route('/crear/centro/admin/procesar', name: 'app_crear_centro_admin_procesar', methods: ['POST'])]
    public function procesarFormulario(Request $request): Response
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
            if ($nombreCentro != "" && $nombreProvincia != "" && !self::centroExistente($nombreCentro)) {
                $datosJson[$tituloJson] = $nuevoCentro;
            } else {
                throw new Exception("Centro vacío o ya existente, o provincia vacía");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        // Codifica los datos actualizados a JSON
        $contenidoActualizado = json_encode($datosJson, JSON_PRETTY_PRINT);

        // Guarda los cambios en el archivo JSON
        file_put_contents($rutaArchivo, $contenidoActualizado);

        //Creamos el centro en la base de datos
        $this->centroService->getCentro();

        // Redirecciona a la ruta 'app_menu_administrador'
        return $this->redirectToRoute('app_menu_administrador');
    }

    public function centroExistente($centro): bool
    {
        $centros = $this->festivoCentroService->getNombreCentros();

        return in_array($centro, $centros);
    }
}
