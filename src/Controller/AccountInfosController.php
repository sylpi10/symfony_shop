<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeInfosType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sylius\Bundle\UserBundle\Form\Model\ChangePassword;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountInfosController extends AbstractController
{
    /**
     * @Route("/account/password", name="account_password")
     */
    public function changePassword(Request $request, UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangeInfosType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {
                // check if old password is ok
            if ($encoder->isPasswordValid($user, $form->get('oldPassword')->getData())) {
                $user->setPassword($encoder->encodePassword($user, $form->get('newPassword')->getData()));
                $user = $form->getData();
                // $manager->persist($user);
                $manager->flush();
                $this->addFlash("success", "Your Infos have been updated");
                return $this->redirectToRoute('account');
            }  else {
                $this->addFlash("error", "Your Actual password is wrong");
                return $this->redirectToRoute('account_password');
            }     
            
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
