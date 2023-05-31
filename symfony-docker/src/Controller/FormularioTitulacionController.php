<?php

namespace App\Controller;

use App\Repository\CentroRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioTitulacionController extends AbstractController
{
    private CentroRepository $centroRepository;

    public function __construct(CentroRepository $centroRepository)
    {
        $this->centroRepository = $centroRepository;
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
}