<?php

namespace App\Controller;

use App\Repository\CentroRepository;
use App\Repository\TitulacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioTitulacionController extends AbstractController
{
    private CentroRepository $centroRepository;
    private TitulacionRepository $titulacionRepository;

    public function __construct(CentroRepository $centroRepository, TitulacionRepository $titulacionRepository)
    {
        $this->centroRepository = $centroRepository;
        $this->titulacionRepository = $titulacionRepository;
    }

    #[Route('/formularios/titulacion', name: 'app_formulario_titulacion')]
    public function index(): Response
    {
        //Obtener los centros
        $centros = $this->centroRepository->findAll();

        $centrosArray = array_map(function($centro) {
            return $centro->getNombre()."-".$centro->getProvincia();
        }, $centros);
        
        //creamos un json de los grupos para pasar al javascript
        $centrosArrayJson = json_encode($centrosArray);

        return $this->render('formularios/titulacion.html.twig', [
            'controller_name' => 'FormularioTitulacionController',
            'centros' => $centrosArrayJson
        ]);
    }

    #[Route('/seleccionar/titulacion', name: 'app_seleccionar_titulacion')]
    public function seleccionarTitulacion(): Response
    {
        //Obtener las titulaciones
        $titulaciones = $this->titulacionRepository->findAll();

        return $this->render('leer/titulacion.html.twig', [
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
            return $centro->getNombre()."-".$centro->getProvincia();
        }, $centros);
        
        //creamos un json de los grupos para pasar al javascript
        $centrosArrayJson = json_encode($centrosArray);
    
        return $this->render('editar/titulacion.html.twig', [
            'controller_name' => 'FormularioTitulacionController',
            'titulacion' => $titulacionJson,
            'centros' => $centrosArrayJson
        ]);
    }
    
}