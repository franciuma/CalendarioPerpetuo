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

    #[Route('/menu', name: 'app_menu_principal')]
    public function principal(): Response
    {
        return $this->render('menus/principal.html.twig');
    }

    //Menú alumnos
    #[Route('/menu/alumno', name: 'app_menu_alumno')]
    public function calendarioAlumno(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        $estado = $request->get("estado");
        $msjPrincipal = $request->get("principal");
        return $this->render('menus/navbarAlumno/calendarioAlumno.html.twig', [
            'mensaje' => $mensaje,
            'msjPrincipal' => $msjPrincipal,
            'estado' => $estado
        ]);
    }

    //Menú docente
    #[Route('/menu/asignaturas/docente', name: 'app_menu_asignaturas_docente')]
    public function asignaturasDocente(): Response
    {
        return $this->render('menus/navbarProfesor/asignaturasDocente.html.twig');
    }

    #[Route('/menu/calendario/docente', name: 'app_menu_calendario_docente')]
    public function calendarioDocente(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        return $this->render('menus/navbarProfesor/calendarioDocente.html.twig', [
            'mensaje' => $mensaje
        ]);
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

    #[Route('/menu/periodos/nacionales/admin', name: 'app_menu_periodos_nacionales_admin')]
    public function periodosNacionalesAdmin(): Response
    {
        return $this->render('menus/navbarAdministrador/periodosNacionalesAdmin.html.twig');
    }

    #[Route('/menu/periodos/locales/admin', name: 'app_menu_periodos_locales_admin')]
    public function periodosLocalesAdmin(): Response
    {
        return $this->render('menus/navbarAdministrador/periodosLocalesAdmin.html.twig');
    }

    #[Route('/menu/periodos/centro/admin', name: 'app_menu_periodos_centro_admin')]
    public function periodosCentroAdmin(): Response
    {
        return $this->render('menus/navbarAdministrador/periodosCentroAdmin.html.twig');
    }

    #[Route('/menu/centro/admin', name: 'app_menu_centro_admin')]
    public function centroAdmin(): Response
    {
        return $this->render('menus/navbarAdministrador/centroAdmin.html.twig');
    }
}
