<?php

namespace App\Controller\Account;

use App\Entity\Address;
use App\Form\AddressType;
use App\Services\AddressesService;
use App\Repository\AddressRepository;
use App\Repository\UserRepository;
use App\Services\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAddressController extends AbstractController
{
    /**
     * @Route("/account/address", name="account_address")
     */
    public function index(): Response
    {   
        // get all addresses from the user
        $addresses = $this->getUser()->getAddresses();
        return $this->render('account/address.html.twig', [
            
            'addresses' => $addresses
        ]);
    }

    /**
     * @Route("/account/add_address", name="add_address")
     */
    public function add(Request $request, EntityManagerInterface $manager, CartService $cartService)
    {
        $user = $this->getUser();
        $address = new Address();
        $form = $this->createform(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $address = $form->getData();
            $address->setUser($user);
            $manager->persist($address);
            $manager->flush();
            if ($cartService->get()) {
                return $this->redirectToRoute('order');
            }
            $this->addFlash("success", "This Address have been created");
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/account/edit_address/{id}", name="edit_address")
     */
    public function edit(AddressRepository $addressRepo, $id, EntityManagerInterface $manager, Request $request)
    {
        $address = $addressRepo->find($id);
        $form = $this->createform(AddressType::class, $address);
        $form->handleRequest($request);
        if (!$address || $address->getUser() != $this->getUser()) {
            return $this->redirectToRoute('account_address');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash("success", "This Address have been updated");
            return $this->redirectToRoute('account_address');
        }
        return $this->render('account/address_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/account/remove_address/{id}", name="delete_address")
     */
    public function remove(AddressesService $service, Address $address): Response
    {
        $service->delete($address);
        return $this->redirectToRoute('account_address');
    }
}
