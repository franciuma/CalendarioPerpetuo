<?php

namespace App\Controller;

use App\Repository\CentroRepository;
use App\Repository\EventoRepository;
use App\Repository\FestivoLocalRepository;
use App\Service\FestivoLocalService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LocalidadAdminController extends AbstractController
{
    const ERROR = "error";
    const SUCCESS = "success";
    const EXITO = "Éxito";
    const FALLO = "Error";
    private CentroRepository $centroRepository;
    private FestivoLocalService $festivoLocalService;
    private FestivoLocalRepository $festivoLocalRepository;
    private EventoRepository $eventoRepository;

    public function __construct(
        CentroRepository $centroRepository,
        FestivoLocalService $festivoLocalService,
        FestivoLocalRepository $festivoLocalRepository,
        EventoRepository $eventoRepository
    )
    {
        $this->centroRepository = $centroRepository;
        $this->eventoRepository = $eventoRepository;
        $this->festivoLocalService = $festivoLocalService;
        $this->festivoLocalRepository = $festivoLocalRepository;
    }

    #[Route('/crear/localidad/admin', name: 'app_crear_localidad_admin')]
    public function index(): Response
    {
        return $this->render('crear/localidad.html.twig');
    }

    //Crear un nodo localidad vacío (solo el título)
    #[Route('/crear/localidad/admin/procesar', name: 'app_crear_localidad_admin_procesar', methods: ['POST'])]
    public function procesarFormulario(Request $request): Response
    {
        $mensaje = "Localidad creada correctamente";
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
            $mensaje = "Localidad ya existente";
            return $this->redirectToRoute('app_menu_localidades_admin',[
                "principal"=>self::FALLO,
                "mensaje" => $mensaje,
                "estado" => self::ERROR
            ]);
        }

        // Codifica los datos actualizados a JSON
        $contenidoActualizado = json_encode($datosJson, JSON_PRETTY_PRINT);

        // Guarda los cambios en el archivo JSON
        file_put_contents($rutaArchivo, $contenidoActualizado);

        // Redirecciona al menú de localidades
        return $this->redirectToRoute('app_menu_localidades_admin',[
            "principal"=>self::EXITO,
            "mensaje" => $mensaje,
            "estado" => self::SUCCESS
            ]);
    }

    #[Route('/seleccionar/editar/localidad/admin', name: 'app_seleccionar_editar_localidad')]
    #[Route('/seleccionar/eliminar/localidad/admin', name: 'app_seleccionar_eliminar_localidad')]
    public function seleccionarLocalidad(Request $request): Response
    {
        $url = $request->getPathInfo();
        if($url == '/seleccionar/editar/localidad/admin') {
            $accion = "Editar localidad";
            $controlador = 'app_seleccionar_editar_localidad';
        } else {
            $accion = "Eliminar localidad";
            $controlador = 'app_seleccionar_eliminar_localidad';
        }

        $localidades = $this->festivoLocalService->getProvincias();

        if($request->isMethod('POST')) {
            $localidad = $request->get('localidad');
            if($accion == "Editar localidad") {
                return $this->redirectToRoute('app_editar_localidad_admin', ['localidad' => $localidad]);
            } else {
                return $this->redirectToRoute('app_eliminar_localidad_admin', ['localidad' => $localidad]);
            }
        }

        return $this->render('leer/localidad.html.twig', [
            'localidades' => $localidades,
            'accion' => $accion,
            'controlador' => $controlador
        ]);
    }

    #[Route('/editar/localidad/admin', name: 'app_editar_localidad_admin')]
    public function editar(Request $request): Response
    {
        $localidad = $request->get('localidad');

        return $this->render('editar/localidad.html.twig', [
            'localidad' => $localidad
        ]);
    }

    #[Route('procesar/editar/localidad', name: 'app_procesar_editar_localidad_admin')]
    public function procesarEditar(Request $request): Response
    {
        $mensaje = "Localidad editada correctamente";

        $localidadAntigua = $request->get('nombreLocalidadAntiguo');
        $localidadNueva = $request->get('nombreLocalidad');

        // Lee el contenido actual del archivo JSON
        $rutaArchivo = '/app/src/resources/festivosLocales.json';
        $contenidoJson = file_get_contents($rutaArchivo);

        // Decodifica el contenido JSON en un array asociativo
        $festivosLocalesArray = json_decode($contenidoJson, true);
        $localidadesArray = array_keys($festivosLocalesArray);

        foreach ($localidadesArray as $localidad) {
            if($localidad == 'festivosLocales'.$localidadAntigua) {
                $nombreNuevoLocal = 'festivosLocales'.$localidadNueva;
                // Cambiar el nombre de la clave
                $festivosLocalesArray[$nombreNuevoLocal] = $festivosLocalesArray[$localidad];
                // Eliminar la clave anterior
                unset($festivosLocalesArray[$localidad]);
            }
        }

        // Codifica los datos actualizados a JSON
        $contenidoActualizado = json_encode($festivosLocalesArray, JSON_PRETTY_PRINT);
        // Guarda los cambios en el archivo JSON
        file_put_contents($rutaArchivo, $contenidoActualizado);

        //Obtenemos los centros con la localidad antigua
        $centros = $this->centroRepository->findByLocalidad($localidadAntigua);
        //Le colocamos la localidad correspondiente a cada centro
        foreach ($centros as $centro) {
            $centro->setProvincia($localidadNueva);
        }
        $this->centroRepository->flush();

        return $this->redirectToRoute('app_menu_localidades_admin',[
            "principal"=>self::EXITO,
            "mensaje" => $mensaje,
            "estado" => self::SUCCESS
        ]);
    }

    #[Route('/eliminar/localidad/admin', name: 'app_eliminar_localidad_admin')]
    public function eliminar(Request $request): Response
    {
        $mensaje = "Localidad eliminada correctamente";
        $localidadEscogida = $request->get('localidad');

        // Lee el contenido actual del archivo JSON
        $rutaArchivo = '/app/src/resources/festivosLocales.json';
        $contenidoJson = file_get_contents($rutaArchivo);

        // Decodifica el contenido JSON en un array asociativo
        $festivosLocalesArray = json_decode($contenidoJson, true);
        $localidadesArray = array_keys($festivosLocalesArray);

        foreach ($localidadesArray as $localidad) {
            if('festivosLocales'.$localidadEscogida == $localidad) {
                // Eliminar la localidad
                unset($festivosLocalesArray[$localidad]);
            }
        }

        //Obtenemos los ids de los festivos locales
        $ids = $this->festivoLocalRepository->obteneridsByProvincia($localidadEscogida);
        //Borramos los eventos asociados
        foreach ($ids as $id) {
            $this->eventoRepository->removeByFestivoLocalId($id);
        }

        //Buscamos los festivos locales para borrarlos
        $festivosLocales = $this->festivoLocalRepository->findByProvincia($localidadEscogida);
        $this->festivoLocalRepository->removeFestivosLocales($festivosLocales);

        // Codifica los datos actualizados a JSON
        $contenidoActualizado = json_encode($festivosLocalesArray, JSON_PRETTY_PRINT);

        // Guarda los cambios en el archivo JSON
        file_put_contents($rutaArchivo, $contenidoActualizado);

        //Actualizamos
        $this->festivoLocalRepository->flush();

        return $this->redirectToRoute('app_menu_localidades_admin',[
            "principal"=>self::EXITO,
            "mensaje" => $mensaje,
            "estado" => self::SUCCESS
            ]);
    }

    public function localidadExistente($localidad): bool
    {
        $localidades = $this->festivoLocalService->getProvincias();

        return in_array($localidad, $localidades);
    }
}
