<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuAlumnoController extends AbstractController
{
    #[Route('/menu/alumno', name: 'app_menu_alumno')]
    public function index(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        return $this->render('menus/alumno.html.twig', [
            'mensaje' => $mensaje
        ]);
    }
}
