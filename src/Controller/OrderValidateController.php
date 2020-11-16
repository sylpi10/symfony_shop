<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderValidateController extends AbstractController
{
    /**
     * @Route("/myorder/thanks/{stripeSessionId}", name="order_validate")
     */
    public function index($stripeSessionId, OrderRepository $orderRepo, EntityManagerInterface $manager): Response
    {
        $order = $orderRepo->findOneByStripeSessionId($stripeSessionId);
        // $order = $manager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);
        
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        // update isPaid status
        if (!$order->getIsPaid()) {
            $order->setIspaid(1);
            $manager->flush();
        }
        // send email to client

        // show order infos

        return $this->render('order_validate/index.html.twig', [
            'order' => $order
        ]);
    }
}
