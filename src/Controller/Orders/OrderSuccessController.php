<?php

namespace App\Controller\Orders;

use App\Services\CartService;
use App\Services\MailService;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
        if ($order->getState() == 0) {
            // empty cart session
            $cartService->remove();
            $order->setState(1);
            $manager->flush();
            //send mail to client

            $mail = new MailService();
            $content = "Hello " .$order->getUser()->getNickName()."</br>Your order in my shop! </br> Lorem ipsum, sit amet consectetur adipisicing elit. Totam dolores a officia ducimus magni cupiditate blanditiis enim cum nam voluptate eos ab dolorem minima, laborum sit accusantium minus fugiat saepe.";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getNickName(), "Your order ok", $content);

        }

        //show order infos


        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
