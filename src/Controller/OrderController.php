<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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


        return $this->render('order/order.html.twig', [
            'form' => $form->createView(),
            'fullCart' => $cartService->getFullCart(),
            'total' => $cartService->getTotal()
        ]);
    }

    /**
     * @Route("/order/recap", name="order_recap", methods={"POST", "GET"})
     */
    public function addOrder(CartService $cartService, Request $request, EntityManagerInterface $manager): Response

    {
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('add_address');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $order = new Order();
            $carrier = $form->get('carrier')->getData();
            $delivery = $form->get('address')->getData();
            $delivery_content = $delivery->getFirstName().' '.$delivery->getLastName();
            if ( $delivery->getCompany()) {
                $delivery_content .='</br>'. $delivery->getCompany();
            }
            $delivery_content .= '</br>'.$delivery->getAddress();
            $delivery_content .= '</br>'.$delivery->getPostalCode().' '.$delivery->getCity();
            $delivery_content .= '</br>'.$delivery->getCountry();
            // dd($delivery_content);

            $order->setUser($this->getUser());
            $order->setCreatedAt(new \DateTime());
            $order->setCarrierName($carrier->getName());
            $order->setCarrierPrice($carrier->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(false);

          
            // save products in orderdetails
            foreach ($cartService->getFullCart() as $product) {
                $orderDetails = new OrderDetails();
                $orderDetails->setMyorder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $manager->persist($orderDetails);

                // dd($product);
            }
            // $manager->flush();

            // dump($checkout_session->id);
            // dd($checkout_session);

            return $this->render('order/order-recap.html.twig', [
                'form' => $form->createView(),
                'fullCart' => $cartService->getFullCart(),
                'total' => $cartService->getTotal(),
                'carrier' => $carrier,
                'delivery' =>$delivery_content,
                
            ]);
        }

        return $this->redirectToRoute('cart');
     
    }
}
