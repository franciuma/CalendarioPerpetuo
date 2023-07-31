<?php

namespace App\Controller;

use App\Service\FestivoLocalService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CrearLocalidadAdminController extends AbstractController
{
    private FestivoLocalService $festivoLocalService;

    public function __construct(FestivoLocalService $festivoLocalService)
    {
        $this->festivoLocalService = $festivoLocalService;
    }

    #[Route('/crear/localidad/admin', name: 'app_crear_localidad_admin')]
    public function index(): Response
    {
        return $this->render('crear/localidad.html.twig', [
            'controller_name' => 'CrearLocalidadAdminController',
        ]);
    }

    //Crear un nodo localidad vacío (solo el título)
    #[Route('/crear/localidad/admin/procesar', name: 'app_crear_localidad_admin_procesar', methods: ['POST'])]
    public function procesarFormulario(Request $request): Response
    {
        // Obtén los datos del formulario
        $nombreLocalidad = $request->request->get('nombreDeLocalidad');

        // Crea un array con los datos de la localidad
        $tituloJson = "festivosLocales".$nombreLocalidad;
        $nuevaLocalidad = [];

        // Lee el contenido actual del archivo JSON
        $rutaArchivo = '/app/src/resources/festivosLocales.json';
        $contenidoJson = file_get_contents($rutaArchivo);

        // Decodifica el contenido JSON en un array asociativo
        $datosJson = json_decode($contenidoJson, true);

        // Agrega la nueva localidad al array
        try {
            if ($nuevaLocalidad != "" && !self::localidadExistente($nombreLocalidad)) {
                $datosJson[$tituloJson] = $nuevaLocalidad;
            } else {
                throw new Exception("Localidad vacía o ya existente");
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        // Codifica los datos actualizados a JSON
        $contenidoActualizado = json_encode($datosJson, JSON_PRETTY_PRINT);

        // Guarda los cambios en el archivo JSON
        file_put_contents($rutaArchivo, $contenidoActualizado);

        // Redirecciona a la ruta 'app_menu_periodos_locales_admin'
        return $this->redirectToRoute('app_menu_periodos_locales_admin');
    }

    public function localidadExistente($localidad): bool
    {
        $localidades = $this->festivoLocalService->getProvincias();

        return in_array($localidad, $localidades);
    }
}
