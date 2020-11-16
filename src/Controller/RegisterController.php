<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use App\Services\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function index(Request $request, EntityManagerInterface $manager, 
    UserPasswordEncoderInterface $encoder, UserRepository $userRepo): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() ) {

            $user->setPassword($encoder->encodePassword($user, $form->get('plainPassword')->getData()));
            $user = $form->getData();
            $user->setRoles($user->getRoles());

            $manager->persist($user);
            $manager->flush();

            $mail = new MailService();
            $content = "Hello " .$user->getNickName()."</br> Welcome to my Shop ! </br> Lorem ipsum, sit amet consectetur adipisicing elit. Totam dolores a officia ducimus magni cupiditate blanditiis enim cum nam voluptate eos ab dolorem minima, laborum sit accusantium minus fugiat saepe.";
            $mail->send($user->getEmail(), $user->getNickName(), "Registration ok", $content);

            $this->addFlash("success", "Your are registered and you can login now");
            return $this->redirectToRoute('app_login');
 
        }
  

        return $this->render('register/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
