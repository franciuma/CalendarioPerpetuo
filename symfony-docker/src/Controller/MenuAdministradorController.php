<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuAdministradorController extends AbstractController
{
    #[Route('/menu/administrador', name: 'app_menu_administrador')]
    public function index(): Response
    {
        return $this->render('menus/administrador.html.twig', [
            'controller_name' => 'MenuAdministradorController',
        ]);
    }
}
