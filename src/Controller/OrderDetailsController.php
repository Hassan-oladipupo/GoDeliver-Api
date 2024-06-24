<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderDetailsController extends AbstractController
{
    #[Route('api/v1/order-details', name: 'app_order_details', methods: ['POST'])]
    public function createOrder(): Response
    {
        return $this->render('order_details/index.html.twig', [
            'controller_name' => 'OrderDetailsController',
        ]);
    }
}
