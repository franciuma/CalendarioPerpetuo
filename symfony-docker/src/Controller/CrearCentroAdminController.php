<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class CrearCentroAdminController extends AbstractController
{
    #[Route('/crear/centro/admin', name: 'app_crear_centro_admin')]
    public function index(): Response
    {
        return $this->render('crear/centro.html.twig', [
            'controller_name' => 'CrearCentroAdminController',
        ]);
    }

    //Crear un centro
    #[Route('/crear/centro/admin/procesar', name: 'app_crear_centro_admin_procesar', methods: ['POST'])]
    public function procesarFormulario(Request $request): Response
    {
        // Obtén los datos del formulario
        $nombreCentro = $request->request->get('nombreDelCentro');
        $nombreProvincia = $request->request->get('nombreDeProvincia');

        // Crea un array con los datos del nuevo centro
        $tituloJson = "festivosCentro{$nombreCentro}-{$nombreProvincia}";
        $nuevoCentro = [];

        // Lee el contenido actual del archivo JSON
        $rutaArchivo = '/app/src/resources/festivosCentro.json';
        $contenidoJson = file_get_contents($rutaArchivo);

        // Decodifica el contenido JSON en un array asociativo
        $datosJson = json_decode($contenidoJson, true);

        // Agrega el nuevo centro al array existente 
        if($nombreCentro != "" && $nombreProvincia != "" ){
            $datosJson[$tituloJson] = $nuevoCentro;
        }

        // Codifica los datos actualizados a JSON
        $contenidoActualizado = json_encode($datosJson, JSON_PRETTY_PRINT);

        // Guarda los cambios en el archivo JSON
        file_put_contents($rutaArchivo, $contenidoActualizado);

        // Redirecciona a la ruta 'app_menu_administrador'
        return $this->redirectToRoute('app_menu_administrador');
    }
}
