<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{

    #[Route('/menu/administrador', name: 'app_menu_administrador')]
    public function admin(): Response
    {
        return $this->render('menus/administrador.html.twig', [
            'controller_name' => 'MenuAdministradorController',
        ]);
    }

    #[Route('/menu/docente', name: 'app_menu_profesor')]
    public function docente(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        return $this->render('menus/profesor.html.twig', [
            'mensaje' => $mensaje
        ]);
    }

    #[Route('/menu/alumno', name: 'app_menu_alumno')]
    public function alumno(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        return $this->render('menus/alumno.html.twig', [
            'mensaje' => $mensaje
        ]);
    }

    #[Route('/menu', name: 'app_menu_principal')]
    public function principal(): Response
    {
        return $this->render('menus/principal.html.twig');
    }

    //Menú docente
    #[Route('/menu/asignaturas/docente', name: 'app_menu_asignaturas_docente')]
    public function asignaturasDocente(): Response
    {
        return $this->render('menus/navbarProfesor/asignaturasDocente.html.twig');
    }

    //Menú administrador
    #[Route('/menu/docentes/admin', name: 'app_menu_docentes_admin')]
    public function docentesAdmin(): Response
    {
        return $this->render('menus/navbarAdministrador/docentesAdmin.html.twig');
    }

    #[Route('/menu/titulaciones/admin', name: 'app_menu_titulaciones_admin')]
    public function titulacionesAdmin(): Response
    {
        return $this->render('menus/navbarAdministrador/titulacionesAdmin.html.twig');
    }
}
