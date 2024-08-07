<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    // METHODE POUR AFFICHER LE DASHBORD
    #[Route('/dashbord', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/dash.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
