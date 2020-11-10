<?php

namespace App\Services;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{

    private SessionInterface $session;
    private ProductRepository $productRepo;

    public function __construct(SessionInterface $session, ProductRepository $productRepo)
    {
        $this->session = $session;
        $this->productRepo = $productRepo;
    }

    public function add(int $id)
    {
       $cart = $this->session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        }else{
            $cart[$id] = 1;
        }

       $this->session->set('cart', $cart);
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function remove()
    {
        return $this->session->remove('cart');
        
    }
   
    public function delete($id)
    {
        $cart = $this->session->get('cart', []);
        unset($cart[$id]);
         // set cart as the new cart
        return $this->session->set('cart', $cart);
        
    }
   
    public function deleteOne($id)
    {
        $cart = $this->session->get('cart', []);

        if (($cart[$id]) > 1) {
            $cart[$id]--;
        }else{
            // $cart[$id] = 0;
            unset($cart[$id]);
        }

       $this->session->set('cart', $cart);
        
    }

    public function getFullCart(): array
    {
        $cart = $this->session->get('cart', []);
        $cartDatas = [];
        
        if ($cart) {
            foreach ($cart as $id => $quantity) {
                $product_obj = $this->productRepo->find($id);
                if (!$product_obj) {
                    $this->delete($id);
                    continue;
                }
                $cartDatas[] = [
                    'product' => $product_obj,
                    'quantity' => $quantity
                ];
            }
          
        }

        return $cartDatas;
    }   

    public function getTotal(): float
    {
        $cartDatas = $this->getFullCart();
        $total = 0;

        foreach ($cartDatas as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        return $total;        
    }
}