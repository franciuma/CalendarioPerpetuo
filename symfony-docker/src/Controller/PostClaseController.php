<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PostClaseController extends AbstractController
{
    #[Route('/post/clase', name: 'app_post')]
    public function index(): void
    {
        //Redirigirlo al calendario
    }
}
