<?php

namespace App\Controller;

use App\Repository\CentroRepository;
use App\Repository\FestivoCentroRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\FestivoCentroService;
use Exception;
use App\Service\CentroService;
use App\Service\FestivoLocalService;
use App\Service\UsuarioService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CentroController extends AbstractController
{
    private $centro;
    private UsuarioService $usuarioService;
    private FestivoCentroService $festivoCentroService;
    private FestivoLocalService $festivoLocalService;
    private CentroService $centroService;
    private CentroRepository $centroRepository;
    private FestivoCentroRepository $festivoCentroRepository;

    public function __construct(
        UsuarioService $usuarioService,
        FestivoCentroService $festivoCentroService,
        FestivoLocalService $festivoLocalService,
        CentroService $centroService,
        CentroRepository $centroRepository,
        FestivoCentroRepository $festivoCentroRepository
    )
    {
        $this->centroRepository = $centroRepository;
        $this->festivoCentroService = $festivoCentroService;
        $this->usuarioService = $usuarioService;
        $this->festivoLocalService = $festivoLocalService;
        $this->festivoCentroRepository = $festivoCentroRepository;
        $this->centroService = $centroService;
    }

    #[Route('/formulario/centro', name: 'app_formulario_centro')]
    public function index(): Response
    {
        $nombreCentrosProvincias = $this->festivoCentroService->getNombreCentroProvincia();
        $conCalendario = false;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        return $this->render('formularios/centro.html.twig', [
            'nombreCentrosProvincias' => $nombreCentrosProvincias,
            'profesores' => $profesores
        ]);
    }

    #[Route('/crear/centro/admin', name: 'app_crear_centro_admin')]
    public function crearCentro(): Response
    {
        $provincias = $this->festivoLocalService->getProvincias();
        $centros = $this->festivoCentroService->getNombreCentroProvincia();

        $centrosJson = json_encode($centros);

        return $this->render('crear/centro.html.twig', [
            'provincias' => $provincias,
            'centros' => $centrosJson
        ]);
    }

    #[Route('/seleccionar/editar/centro/admin', name: 'app_seleccionar_editar_centro')]
    #[Route('/seleccionar/eliminar/centro/admin', name: 'app_seleccionar_eliminar_centro')]
    public function seleccionarCentro(Request $request, SessionInterface $session): Response
    {
        $url = $request->getPathInfo();
        if($url == '/seleccionar/editar/centro/admin') {
            $accion = "Editar centro";
            $controlador = 'app_seleccionar_editar_centro';
        } else {
            $accion = "Eliminar centro";
            $controlador = 'app_seleccionar_eliminar_centro';
        }
        $centros = $this->festivoCentroService->getNombreCentroProvincia();

        if($request->isMethod('POST')) {
            $centro = $request->get('centro');
            $centroFormato = explode("-",$centro);
            $centroObjeto = $this->centroRepository->findOneByProvinciaCentro($centroFormato[1], $centroFormato[0]);
            $centroObjetoId = $centroObjeto->getId();
            if($accion == "Editar centro") {
                $session->set('centroId', $centroObjetoId);
                return $this->redirectToRoute('app_editar_centro', ['centroId' => $centroObjetoId]);
            } else {
                return $this->redirectToRoute('app_eliminar_centro', ['centroId' => $centroObjetoId]);
            }
        }

        return $this->render('leer/centro.html.twig', [
            'centros' => $centros,
            'accion' => $accion,
            'controlador' => $controlador
        ]);
    }

    #[Route('/editar/centro/admin', name: 'app_editar_centro')]
    public function editarCentro(Request $request, SessionInterface $session): Response
    {
        if($request->isMethod('POST') && ($request->request->has('nombreCentro') || $request->request->has('provincia'))) {
            $nombreCentro = $request->get('nombreCentro');
            $provincia = $request->get('provincia');
            $centroId = $session->get('centroId');
            return $this->redirectToRoute('app_editar_centro_admin',
                [
                    'nombreDelCentro' => $nombreCentro,
                    'nombreDeProvincia' => $provincia,
                    'centroId' => $centroId
                ]);
        }

        $centroId = $request->get('centroId');
        $centroObjeto = $this->centroRepository->find($centroId);
        $provincias = $this->festivoLocalService->getProvincias();

        return $this->render('editar/centro.html.twig', [
            'centro' => $centroObjeto,
            'provincias' => $provincias
        ]);
    }

    #[Route('/editar/centro/admin/procesar', name: 'app_editar_centro_admin')]
    public function procesarFormularioEditar(Request $request)
    {
        // Obtenemos los datos del formulario
        $nombreCentro = $request->get('nombreDelCentro');
        $nombreProvincia = $request->get('nombreDeProvincia');
        $centroAntiguoId = $request->get('centroId');
        $centroAntiguo = $this->centroRepository->find($centroAntiguoId);

        // Obtenemos el nombre y provincia anteriores
        $nombreCentroAntiguo = $centroAntiguo->getNombre();
        $nombreProvinciaAntiguo = $centroAntiguo->getProvincia();

        // Lee el contenido actual del archivo JSON
        $rutaArchivo = '/app/src/resources/festivosCentro.json';
        $contenidoJson = file_get_contents($rutaArchivo);

        // Decodifica el contenido JSON en un array asociativo
        $festivosCentroArray = json_decode($contenidoJson, true);
        $centrosArray = array_keys($festivosCentroArray);

        foreach ($centrosArray as $centro) {
            if($centro == 'festivosCentro'.$nombreCentroAntiguo.'-'.$nombreProvinciaAntiguo) {
                $nombreNuevoCentro = 'festivosCentro'.$nombreCentro.'-'.$nombreProvincia;
                // Cambiar el nombre de la clave
                $festivosCentroArray[$nombreNuevoCentro] = $festivosCentroArray[$centro];

                // Eliminar la clave anterior
                unset($festivosCentroArray[$centro]);
            }
        }

        // Codifica los datos actualizados a JSON
        $contenidoActualizado = json_encode($festivosCentroArray, JSON_PRETTY_PRINT);

        // Guarda los cambios en el archivo JSON
        file_put_contents($rutaArchivo, $contenidoActualizado);

        //Editar el centro en la base de datos
        $centroAntiguo->setNombre($nombreCentro);
        $centroAntiguo->setProvincia($nombreProvincia);
        $this->centroRepository->flush();

        return $this->redirectToRoute('app_menu_periodos_centro_admin');
    }

    #[Route('/eliminar/centro/admin', name: 'app_eliminar_centro')]
    public function eliminarCentro(Request $request): Response
    {
        $centroId = $request->get('centroId');
        $centroObjeto = $this->centroRepository->find($centroId);
        $centroNombre = $centroObjeto->getNombre();
        $centroProvincia = $centroObjeto->getProvincia();

        // Lee el contenido actual del archivo JSON
        $rutaArchivo = '/app/src/resources/festivosCentro.json';
        $contenidoJson = file_get_contents($rutaArchivo);

        // Decodifica el contenido JSON en un array asociativo
        $festivosCentroArray = json_decode($contenidoJson, true);
        $centrosArray = array_keys($festivosCentroArray);

        foreach ($centrosArray as $centro) {
            if($centro == 'festivosCentro'.$centroNombre.'-'.$centroProvincia) {
                // Eliminar el centro
                unset($festivosCentroArray[$centro]);
            }
        }

        //Buscamos los festivos del centro para borrarlos
        $festivosCentro = $this->festivoCentroRepository->findByCentroId($centroId);
        $this->festivoCentroRepository->removeFestivosCentro($festivosCentro);

        // Codifica los datos actualizados a JSON
        $contenidoActualizado = json_encode($festivosCentroArray, JSON_PRETTY_PRINT);

        // Guarda los cambios en el archivo JSON
        file_put_contents($rutaArchivo, $contenidoActualizado);

        //Borramos el centro
        $this->centroRepository->remove($centroObjeto, true);
        return $this->redirectToRoute('app_menu_periodos_centro_admin');
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

        // Redirecciona a la ruta 'app_menu_periodos_centro_admin'
        return $this->redirectToRoute('app_menu_periodos_centro_admin');
    }

    public function centroExistente($nombreCentro, $nombreProvincia): bool
    {
        $centros = $this->festivoCentroService->getNombreCentroProvincia();
        return in_array($nombreCentro.'-'.$nombreProvincia, $centros);
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
