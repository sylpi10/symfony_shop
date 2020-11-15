<?php

namespace App\Controller;

use App\Services\CartService;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class StripeController extends AbstractController
{
    /**
     * @Route("/order/create-session", name="stripe_sreate_session")
     */
    public function index(CartService $cartService): Response
    {
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';
        $products_for_stripe = [];

        foreach ($cartService->getFullCart() as $product) {
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [$YOUR_DOMAIN."/uploads/".$product['product']->getPicture()],
                    ],
                    ],
                    'quantity' => $product['quantity'],
                ];

        }

        Stripe::setApiKey('sk_test_51HnUYOKpJz9wyF2lAD5iBgC2vYpk92yD0dShYeiyeAsVVbxDgLNfcXFdJy3BSrJXF25tC5uYArQoKPiki5UW70nD00ioJEwQoe');

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                $products_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
