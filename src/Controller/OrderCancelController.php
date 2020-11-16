<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    /**
     * @Route("/order/cancel/{stripeSessionId}", name="order_cancel")
    */                                 
    public function index($stripeSessionId, OrderRepository $orderRepo, EntityManagerInterface $manager): Response
    {

        $order = $orderRepo->findOneByStripeSessionId($stripeSessionId);
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        // send mail ?

        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
