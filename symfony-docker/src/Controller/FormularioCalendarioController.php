<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProfesorRepository;
use App\Repository\AsignaturaRepository;

class FormularioCalendarioController extends AbstractController
{

    private ProfesorRepository $profesorRepository;
    private AsignaturaRepository $asignaturaRepository;

    public function __construct(ProfesorRepository $profesorRepository, AsignaturaRepository $asignaturaRepository){
        $this->profesorRepository = $profesorRepository;
        $this->asignaturaRepository = $asignaturaRepository;

    }

    #[Route('/formulario/calendario', name: 'app_formulario_calendario')]
    public function index(): Response
    {
        //Obtener las asignaturas por nombre totales:
        $asignaturas = $this->asignaturaRepository->findAll();

        $titulosAsignaturas = array_map(function ($asignatura) {
            return $asignatura->getNombre();
        }, $asignaturas);

        //Obtener los grupos pertenecientes dado un profesor
        $grupos = $this->profesorRepository->findGruposByProfesor("Francisco","López");

        $gruposArray = array_map(function($grupo) {
            return [
                'id' => $grupo->getId(),
                'letra' => $grupo->getLetra(),
                'horario' => $grupo->getHorario(),
                'diasTeoria' => $grupo->getDiasTeoria(),
                'diasPractica' => $grupo->getDiasPractica(),
                'asignatura' => $grupo->getAsignatura()->getNombre()
            ];
        }, $grupos);
        //creamos un json de los grupos para pasar al javascript
        $gruposJson = json_encode($gruposArray);

        return $this->render('formularios/calendario.html.twig', [
            'controller_name' => 'FormularioCalendarioController',
            'grupos' => $gruposJson,
            'asignaturas' => $titulosAsignaturas
        ]);
    }
}
