<?php

namespace App\Controller\Orders;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderErrorController extends AbstractController
{
    /**
     * @Route("/order/error/{stripeSessionId}", name="order_error")
     */
    public function index(OrderRepository $orderRepo, $stripeSessionId, EntityManagerInterface $manager): Response
    {
        $order = $orderRepo->findOneByStripeSessionId($stripeSessionId);


        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('order_error/index.html.twig', [
            'order' => $order
        ]);
    }
}
