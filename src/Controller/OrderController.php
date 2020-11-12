<?php

namespace App\Controller;

use App\Form\OrderType;
use App\Services\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/order", name="order")
     */
    public function index(CartService $cartService, Request $request): Response

    {
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('add_address');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
        }

        return $this->render('order/order.html.twig', [
            'form' => $form->createView(),
            'fullCart' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()
        ]);
    }
}
