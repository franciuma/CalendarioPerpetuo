<?php

namespace App\Controller;

use App\Repository\AsignaturaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\AsignaturaService;
use App\Service\LeccionService;
use App\Service\TitulacionService;
use Symfony\Component\HttpFoundation\Request;

class PostAsignaturaController extends AbstractController
{
    private AsignaturaService $asignaturaService;
    private AsignaturaRepository $asignaturaRepository;
    private LeccionService $leccionService;
    private TitulacionService $titulacionService;

    public function __construct(
        AsignaturaService $asignaturaService,
        AsignaturaRepository $asignaturaRepository,
        LeccionService $leccionService,
        TitulacionService $titulacionService
    )
    {
        $this->asignaturaService = $asignaturaService;
        $this->asignaturaRepository = $asignaturaRepository;
        $this->leccionService = $leccionService;
        $this->titulacionService = $titulacionService;
    }

    #[Route('/post/asignatura', name: 'app_post_asignatura')]
    public function index(): Response
    {
        //Persistir las titulaciones y devolver array de objetos Titulacion
        $this->titulacionService->getTitulaciones();
        //Persistir las asignaturas del JSON a la bd
        $this->asignaturaService->getAsignaturas();

        return $this->render('posts/asignatura.html.twig', [
            'controller_name' => 'PostAsignaturaController',
        ]);
    }

    #[Route('/post/asignatura/editada', name: 'app_post_asignatura_editada')]
    public function postEditada(Request $request): Response
    {
        $asignaturaId = $request->get('asignatura');
        $asignatura = $this->asignaturaRepository->find($asignaturaId);
        $this->asignaturaService->editarAsignatura($asignatura);
        $this->leccionService->editarLecciones($asignatura->getLecciones());
        $mensaje = "Asignatura editada correctamente";

        //Guardamos los cambios en la base de datos
        $this->asignaturaRepository->save($asignatura, true);

        return $this->redirectToRoute('app_menu_calendario_docente',["mensaje" => $mensaje]);
    }
}
