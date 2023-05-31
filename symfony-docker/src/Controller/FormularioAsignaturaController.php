<?php

namespace App\Controller;

use App\Repository\TitulacionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormularioAsignaturaController extends AbstractController
{
    private TitulacionRepository $titulacionRepository;

    public function __construct( TitulacionRepository $titulacionRepository )
    {
        $this->titulacionRepository = $titulacionRepository;
    }

    #[Route('/formulario/asignatura', name: 'app_formulario_asignatura')]
    public function index(): Response
    {
        $titulaciones = $this->titulacionRepository->findAll();

        $titulacionesArray = array_map(function($titulacion) {
            return $titulacion->getAbreviatura()."-".$titulacion->getCentro()->getProvincia();
        }, $titulaciones);

        //Eliminamos los elementos repetidos
        $titulacionesArray = array_unique($titulacionesArray);
        //creamos un json de los grupos para pasar al javascript
        $titulacionesArrayJson = json_encode($titulacionesArray);

        return $this->render('formularios/asignatura.html.twig', [
            'controller_name' => 'FormularioAsignaturaController',
            'titulaciones' => $titulacionesArrayJson
        ]);
    }
}
