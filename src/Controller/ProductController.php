<?php

namespace App\Controller;

use App\Entity\Product\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(ProductRepository $repo): Response
    {
        $products = $repo->findAll();
        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/product/{slug}", name="product_detail")
     */
    public function detail(ProductRepository $repo, $slug): Response
    {
        $product = $repo->findOneBySlug($slug);

        if (!$product) {
            return $this->redirectToRoute('product');
        }

        return $this->render('product/detail.html.twig', [
            'product' => $product
        ]);
    }
}
