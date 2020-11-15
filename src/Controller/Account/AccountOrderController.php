<?php

namespace App\Controller\Account;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountOrderController extends AbstractController
{
    /**
     * @Route("/account/my-orders", name="account_orders")
     */
    public function index(OrderRepository $orderRepo): Response
    {
        $orders = $orderRepo->findBySuccessOrders($this->getUser());
        // $orderDetails = [];
     
        return $this->render('account/orders.html.twig', [
            'orders' => $orders
        ]);
    }

    /**
     * @Route("/account/my-orders/{reference}", name="account_order_details")
     */
    public function details($reference, OrderRepository $orderRepo): Response
    {
        $order = $orderRepo->findOneByReference($reference);
        if (!$order || $order->getUser() != $this->getUser() ) {
            return $this->redirectToRoute('account_orders');
        }

        return $this->render('account/orders-details.html.twig', [
            'reference' => $reference,
            'order' => $order
        ]);
    }
}
