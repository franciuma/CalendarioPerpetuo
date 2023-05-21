<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AniadirFestivoLocalAdminController extends AbstractController
{
    #[Route('/aniadir/festivo/local/admin', name: 'app_aniadir_festivo_local_admin')]
    public function index(): Response
    {
        return $this->render('aniadir_festivo_local_admin/index.html.twig', [
            'controller_name' => 'AniadirFestivoLocalAdminController',
        ]);
    }
}
