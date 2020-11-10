<?php

namespace App\Controller;

use App\Services\CartService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(CartService $cart): Response
    {   
        $fullCart = $cart->getFullCart();
        $totalPrice = $cart->getTotal();

        return $this->render('cart/cart.html.twig', [
            'cart' => $cart->get(),
            'fullCart' => $fullCart,
            "totalPrice" => $totalPrice
        ]);
    }
  
    /**
     * @Route("/cart/add/{id}", name="add_to_cart")
     */
    public function addCart(CartService $cart, $id): Response
    {
       $cart->add($id);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/remove", name="remove_my_cart")
     */
    public function removeCart(CartService $cart): Response
    {
       $cart->remove();
        return $this->redirectToRoute('product');
    }
    
    /**
     * @Route("/cart/delete/{id}", name="delete")
     */
    public function delete(CartService $cart, $id): Response
    {
       $cart->delete($id);
        return $this->redirectToRoute('cart');
    }
    /**
     * @Route("/cart/deleteOne/{id}", name="delete_one")
     */
    public function deleteOne(CartService $cart, $id): Response
    {
       $cart->deleteOne($id);
        return $this->redirectToRoute('cart');
    }

}
