<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Services\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class GlobalController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ProductRepository $repo, SessionInterface $session, $isBest = 1): Response
    {
        $products = $repo->findByIsBest($isBest);
        return $this->render('global/index.html.twig', [
          'products' => $products
        ]);
    }
}
