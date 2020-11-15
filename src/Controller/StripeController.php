<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class StripeController extends AbstractController
{
    /**
     * @Route("/order/create-session/{reference}", name="stripe_sreate_session")
     */
    public function index($reference, EntityManagerInterface $manager,
     ProductRepository $productRepo, OrderRepository $orderRepo): Response
    {
        $order = $orderRepo->findOneByReference($reference);

        if (!$order) {
            new JsonResponse((['error' => 'order']));
        }

        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $products_for_stripe = [];

        foreach ($order->getOrderDetails() as $product) {
            $product_object = $productRepo->findOneByName($product->getProduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product_object->getPicture()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];

        }

        // carrier infos
        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
                ],
                'quantity' => 1,
            ];

        Stripe::setApiKey('sk_test_51HnUYOKpJz9wyF2lAD5iBgC2vYpk92yD0dShYeiyeAsVVbxDgLNfcXFdJy3BSrJXF25tC5uYArQoKPiki5UW70nD00ioJEwQoe');

        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $products_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/order/thanks/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/order/error/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);

        $manager->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
