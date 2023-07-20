<?php

namespace App\Controller;

use App\Repository\AsignaturaRepository;
use App\Repository\CentroRepository;
use App\Repository\TitulacionRepository;
use App\Service\FestivoCentroService;
use App\Service\FestivoLocalService;
use App\Service\FestivoNacionalService;
use App\Service\UsuarioService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    private AsignaturaRepository $asignaturaRepository;
    private CentroRepository $centroRepository;
    private UsuarioService $usuarioService;
    private TitulacionRepository $titulacionRepository;
    private FestivoNacionalService $festivoNacionalService;
    private FestivoCentroService $festivoCentroService;
    private FestivoLocalService $festivoLocalService;

    public function __construct( 
        AsignaturaRepository $asignaturaRepository,
        CentroRepository $centroRepository,
        TitulacionRepository $titulacionRepository,
        UsuarioService $usuarioService,
        FestivoNacionalService $festivoNacionalService,
        FestivoLocalService $festivoLocalService,
        FestivoCentroService $festivoCentroService
    )
    {
        $this->asignaturaRepository = $asignaturaRepository;
        $this->centroRepository = $centroRepository;
        $this->usuarioService = $usuarioService;
        $this->titulacionRepository = $titulacionRepository;
        $this->festivoNacionalService = $festivoNacionalService;
        $this->festivoLocalService = $festivoLocalService;
        $this->festivoCentroService = $festivoCentroService;
    }

    #[Route('/menu/administrador', name: 'app_menu_administrador')]
    public function admin(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        return $this->render('menus/administrador.html.twig', [
            'mensaje' => $mensaje
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
    public function asignaturasDocente(Request $request): Response
    {
        $asignaturas = $this->asignaturaRepository->findAllNombre();
        $mensaje = $request->get("mensaje");
        return $this->render('menus/navbarProfesor/asignaturasDocente.html.twig',[
            'mensaje' => $mensaje,
            'asignaturaLista' => $asignaturas
        ]);
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
        $conCalendario = false;
        $profesores = $this->usuarioService->getAllProfesoresNombreCompleto($conCalendario);

        return $this->render('menus/navbarAdministrador/docentesAdmin.html.twig',[
            'profesorLista' => $profesores
        ]);
    }

    #[Route('/menu/titulaciones/admin', name: 'app_menu_titulaciones_admin')]
    public function titulacionesAdmin(): Response
    {
        $titulaciones = $this->titulacionRepository->findAllNombre();

        return $this->render('menus/navbarAdministrador/titulacionesAdmin.html.twig' ,[
            'titulacionLista' => $titulaciones
        ]);
    }

    #[Route('/menu/periodos/nacionales/admin', name: 'app_menu_periodos_nacionales_admin')]
    public function periodosNacionalesAdmin(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        $festivosNacionales = $this->festivoNacionalService->getFestivosNacionalesNombres();
        return $this->render('menus/navbarAdministrador/periodosNacionalesAdmin.html.twig', [
            'mensaje' => $mensaje,
            'festivosNacionalesLista' => $festivosNacionales
        ]);
    }

    #[Route('/menu/periodos/locales/admin', name: 'app_menu_periodos_locales_admin')]
    public function periodosLocalesAdmin(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        $festivosLocales = $this->festivoLocalService->getFestivosLocalesNombres();
        return $this->render('menus/navbarAdministrador/periodosLocalesAdmin.html.twig', [
            'mensaje' => $mensaje,
            'festivosLocalesLista' => $festivosLocales
        ]);
    }

    #[Route('/menu/localidades/admin', name: 'app_menu_localidades_admin')]
    public function LocalidadesAdmin(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        $localidades = $this->festivoLocalService->getProvincias();
        return $this->render('menus/navbarAdministrador/localidadAdmin.html.twig', [
            'mensaje' => $mensaje,
            'localidadLista' => $localidades
        ]);
    }

    #[Route('/menu/periodos/centro/admin', name: 'app_menu_periodos_centro_admin')]
    public function periodosCentroAdmin(Request $request): Response
    {
        $mensaje = $request->get("mensaje");
        $festivosCentro = $this->festivoCentroService->getFestivosCentroNombres();
        return $this->render('menus/navbarAdministrador/periodosCentroAdmin.html.twig', [
            'mensaje' => $mensaje,
            'festivosCentroLista' => $festivosCentro
        ]);
    }

    #[Route('/menu/centro/admin', name: 'app_menu_centro_admin')]
    public function centroAdmin(): Response
    {
        $centros = $this->centroRepository->findAllNombre();
        return $this->render('menus/navbarAdministrador/centroAdmin.html.twig',[
            'centroLista' => $centros
        ]);
    }
}
