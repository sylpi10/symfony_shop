<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\SliderRepository;
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
    public function index(ProductRepository $repo, SliderRepository $sliderRepo, $isBest = 1): Response
    {
        $sliders = $sliderRepo->findAll();
        $products = $repo->findByIsBest($isBest);
        return $this->render('global/index.html.twig', [
          'products' => $products,
          'sliders' => $sliders
        ]);
    }
}
