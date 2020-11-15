<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    /**
     * @Route("/order/thanks/{stripeSessionId}", name="order_success")
     */
    public function index(CartService $cartService, OrderRepository $orderRepo, 
                        $stripeSessionId, EntityManagerInterface $manager): Response
    {
        $order = $orderRepo->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }
        // set isPaid status to 1
        if (!$order->getIsPaid()) {
            // empty cart session
            $cartService->remove();
            $order->setIsPaid(1);
            $manager->flush();
            //send mail to client
        }

        //show order infos


        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
