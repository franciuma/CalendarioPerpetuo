<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuPrincipalController extends AbstractController
{
    #[Route('/menu', name: 'app_menu_principal')]
    public function index(): Response
    {
        return $this->render('menu_principal/index.html.twig', [
            'controller_name' => 'MenuPrincipalController',
        ]);
    }
}
