<?php

namespace App\Controller;

use App\Entity\Clase;
use App\Repository\AsignaturaRepository;
use App\Repository\CentroRepository;
use App\Repository\ClaseRepository;
use App\Repository\EventoRepository;
use App\Repository\TitulacionRepository;
use App\Service\TitulacionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioTitulacionController extends AbstractController
{
    private CentroRepository $centroRepository;
    private TitulacionRepository $titulacionRepository;
    private EventoRepository $eventoRepository;
    private ClaseRepository $claseRepository;
    private AsignaturaRepository $asignaturaRepository;
    private TitulacionService $titulacionService;

    public function __construct(
        AsignaturaRepository $asignaturaRepository,
        CentroRepository $centroRepository,
        TitulacionRepository $titulacionRepository,
        TitulacionService $titulacionService,
        EventoRepository $eventoRepository,
        ClaseRepository $claseRepository
    ) {
        $this->asignaturaRepository = $asignaturaRepository;
        $this->centroRepository = $centroRepository;
        $this->claseRepository = $claseRepository;
        $this->titulacionRepository = $titulacionRepository;
        $this->titulacionService = $titulacionService;
        $this->eventoRepository = $eventoRepository;
    }

    #[Route('/formularios/titulacion', name: 'app_formulario_titulacion')]
    public function index(): Response
    {
        //Obtener los centros
        $centros = $this->centroRepository->findAllNombresProvincias();

        //creamos un json de los grupos para pasar al javascript
        $centrosArrayJson = json_encode($centros);

        return $this->render('formularios/titulacion.html.twig', [
            'controller_name' => 'FormularioTitulacionController',
            'centros' => $centrosArrayJson
        ]);
    }

    #[Route('/seleccionar/titulacion', name: 'app_seleccionar_titulacion')]
    #[Route('/lista/titulacion', name: 'app_lista_titulacion')]
    public function seleccionarTitulacion(Request $request): Response
    {
        $rutaActual = $request->getPathInfo();

        //Obtener las titulaciones
        $titulaciones = $this->titulacionRepository->findAll();

        //Obtener los nombres de las titulaciones
        $titulacionesArray = array_map(function($titulacion) {
            return $titulacion->getNombreTitulacion()." - ".$titulacion->getCentro()->getProvincia();
        }, $titulaciones);

        if($rutaActual == '/seleccionar/titulacion') {
            return $this->render('leer/titulacion.html.twig', [
                'titulaciones' => $titulaciones
            ]);
        } else {
            return $this->render('listar/titulacion.html.twig', [
                'titulaciones' => $titulacionesArray
            ]);
        }
    }

    #[Route('/eliminar/titulacion', name: 'app_eliminar_titulacion')]
    public function eliminarTitulacion(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Obtener id de la titulación escogida
            $titulacionId = $request->get('nombreDeTitul');

            //Obtenemos el objeto titulacion
            $titulacionObjeto = $this->titulacionRepository->find($titulacionId);

            //Borramos los eventos asociados a la titulacion
            $asignaturas = $titulacionObjeto->getAsignatura();
            foreach ($asignaturas as $asignatura) {
                $clases = $this->claseRepository->findByAsignatura($asignatura->getId());
                foreach ($clases as $clase) {
                    $evento = $this->eventoRepository->findByClaseId($clase->getId());
                    $dia = $evento->getDia();
                    $this->eventoRepository->remove($evento);
                    // Obtén la colección de eventos del objeto Dia
                    $eventos = $dia->getEventos();
                    $hayEventoClase = false;
                    foreach ($eventos as $evento) {
                        if($evento instanceof Clase) {
                            $hayEventoClase = true;
                            break;
                        }
                    }
                    if(!$hayEventoClase) {
                        $dia->setHayClase(false);
                    }
                }
                $this->asignaturaRepository->remove($asignatura);
            }
            //Borramos la titulación
            $this->titulacionRepository->remove($titulacionObjeto);
            $this->titulacionRepository->flush();
            $mensaje = "Titulación eliminada correctamente";
            return $this->redirectToRoute('app_menu_titulaciones_admin',["mensaje" => $mensaje]);
        }
        //Obtener las titulaciones
        $titulaciones = $this->titulacionRepository->findAll();

        return $this->render('eliminar/titulacion.html.twig', [
            'titulaciones' => $titulaciones
        ]);
    }

    #[Route('/editar/titulacion', name: 'app_editar_titulacion')]
    public function editarTitulacion(Request $request): Response
    {
        // Obtener id de la titulación escogida
        $titulacionId = $request->get('nombreDeTitul');
    
        // Obtener la titulacion
        $titulacionObjeto = $this->titulacionRepository->find($titulacionId);
    
        $titulacionArray = [
            'id' => $titulacionObjeto->getId(),
            'nombre' => $titulacionObjeto->getNombreTitulacion(),
            'abreviatura' => $titulacionObjeto->getAbreviatura(),
            'centro' => $titulacionObjeto->getCentro()->getNombre()."-".$titulacionObjeto->getCentro()->getProvincia()
        ];
    
        // Convertir la titulacion a formato JSON
        $titulacionJson = json_encode($titulacionArray);

        //Obtener los centros
        $centros = $this->centroRepository->findAll();

        $centrosArray = array_map(function($centro) {
            return ['nombreProvincia' => $centro->getNombre()."-".$centro->getProvincia()];
        }, $centros);

        //creamos un json de los grupos para pasar al javascript
        $centrosArrayJson = json_encode($centrosArray);
    
        return $this->render('editar/titulacion.html.twig', [
            'controller_name' => 'FormularioTitulacionController',
            'titulacion' => $titulacionJson,
            'centros' => $centrosArrayJson
        ]);
    }

    #[Route('/post/titulacion', name: 'app_post_titulacion')]
    #[Route('/post/titulacion/editado', name: 'app_post_titulacion_editado')]
    public function post(Request $request): Response
    {
        if(($request->getPathInfo() == '/post/titulacion')) {
            $mensaje = "Titulación/es creada/s correctamente";
            //Persistimos las titulaciones
            $this->titulacionService->getTitulaciones();
        } else {
            $mensaje = "Titulación editada correctamente";
            $titulacionId = $request->get('titulacion');
            $titulacionObjeto = $this->titulacionRepository->find($titulacionId);
            $this->titulacionService->editarTitulacion($titulacionObjeto);
        }

        return $this->redirectToRoute('app_menu_titulaciones_admin',["mensaje" => $mensaje]);
    }
}