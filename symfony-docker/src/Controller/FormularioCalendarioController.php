<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProfesorRepository;

class FormularioCalendarioController extends AbstractController
{

    private ProfesorRepository $profesorRepository;

    public function __construct(ProfesorRepository $profesorRepository){
        $this->profesorRepository = $profesorRepository;
    }

    #[Route('/formulario/calendario', name: 'app_formulario_calendario')]
    public function index(): Response
    {

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
        
        $gruposJson = json_encode($gruposArray);

        return $this->render('formularios/calendario.html.twig', [
            'controller_name' => 'FormularioCalendarioController',
            'grupos' => $gruposJson
        ]);
    }
}
