<?php

namespace App\Controller;

use App\Data\Search;
use App\Form\SearchType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product")
     */
    public function index(ProductRepository $repo, Request $request): Response
    {
        
        $search = new Search();
        $form = $this->createForm(SearchType::class, $search);
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $products = $repo->filterSearch($search);
        }else {
            $products = $repo->findAll();
        }

        return $this->render('product/index.html.twig', [
            'products' => $products,
            'form' => $form->createView()
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
