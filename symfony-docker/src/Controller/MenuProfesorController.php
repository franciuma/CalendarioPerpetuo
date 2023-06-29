<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuProfesorController extends AbstractController
{
    #[Route('/menu/docente', name: 'app_menu_profesor')]
    public function index(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        return $this->render('menus/profesor.html.twig', [
            'mensaje' => $mensaje
        ]);
    }
}
