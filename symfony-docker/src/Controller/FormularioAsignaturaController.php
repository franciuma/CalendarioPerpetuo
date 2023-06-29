<?php

namespace App\Controller;

use App\Repository\AsignaturaRepository;
use App\Repository\TitulacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioAsignaturaController extends AbstractController
{
    private TitulacionRepository $titulacionRepository;
    private AsignaturaRepository $asignaturaRepository;

    public function __construct( 
        TitulacionRepository $titulacionRepository,
        AsignaturaRepository $asignaturaRepository
    )
    {
        $this->titulacionRepository = $titulacionRepository;
        $this->asignaturaRepository = $asignaturaRepository;
    }

    #[Route('/formulario/asignatura', name: 'app_formulario_asignatura')]
    public function index(): Response
    {
        $titulacionesArrayJson = self::obtenerTitulaciones();

        return $this->render('formularios/asignatura.html.twig', [
            'controller_name' => 'FormularioAsignaturaController',
            'titulaciones' => $titulacionesArrayJson
        ]);
    }

    #[Route('/seleccionar/editar/asignatura', name: 'app_seleccionar_editar_asignatura')]
    public function seleccionar(Request $request): Response
    {
        $asignaturas = $this->asignaturaRepository->findAll();

        if($request->isMethod('POST')) {
            $asignaturaId = $request->get('idAsig');

            return $this->redirectToRoute('app_editar_asignatura',['id' => $asignaturaId]);
        }

        return $this->render('leer/asignatura.html.twig', [
            'controller_name' => 'FormularioAsignaturaController',
            'asignaturas' => $asignaturas
        ]);
    }

    #[Route('/editar/asignatura', name: 'app_editar_asignatura')]
    public function editar(Request $request): Response
    {
        $asignaturaId = $request->get('id');
        $asignatura = $this->asignaturaRepository->find($asignaturaId);
        $lecciones = $asignatura->getLecciones();

        $leccionesArray = array_map(function($leccion) {
            return [
                'id' => $leccion->getId(),
                'titulo' => $leccion->getTitulo(),
                'asignaturaId' => $leccion->getAsignatura()->getId(),
                'modalidad' => $leccion->getModalidad(),
                'abreviatura' => $leccion->getAbreviatura()
            ];
        }, $lecciones->toArray());

        $leccionesJson = json_encode($leccionesArray);
        
        $asignaturaArray = 
            [
                'id' => $asignatura->getId(),
                'asignatura' => $asignatura->getNombre(),
                'abreviatura' => $asignatura->getAbreviatura(),
                'cuatrimestre' => $asignatura->getCuatrimestre(),
                'titulacion' => $asignatura->getTitulacion()->getAbreviatura()."-".$asignatura->getTitulacion()->getCentro()->getProvincia()
            ];

        $asignaturaJson = json_encode($asignaturaArray);

        $titulacionesArrayJson = self::obtenerTitulaciones();

        return $this->render('editar/asignatura.html.twig', [
            'asignatura' => $asignaturaJson,
            'lecciones' => $leccionesJson,
            'asignaturaid' => $asignaturaId,
            'titulaciones' => $titulacionesArrayJson
        ]);
    }

    public function obtenerTitulaciones()
    {
        $titulaciones = $this->titulacionRepository->findAll();

        $titulacionesArray = array_map(function($titulacion) {
            return $titulacion->getAbreviatura()."-".$titulacion->getCentro()->getProvincia();
        }, $titulaciones);

        //Eliminamos los elementos repetidos
        $titulacionesArray = array_unique($titulacionesArray);
        //creamos un json de los grupos para pasar al javascript
        $titulacionesArrayJson = json_encode($titulacionesArray);

        return $titulacionesArrayJson;
    }
}
