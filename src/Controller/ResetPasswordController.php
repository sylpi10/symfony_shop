<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Services\MailService;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ResetPasswordRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/forgot-password", name="reset_password")
     */
    public function index(Request $request, EntityManagerInterface $mananger, UserRepository $userRepo): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($request->get('email')) {
            $user = $userRepo->findOneByEmail($request->get('email'));
            if ($user) {
                // save in bd reset_pw with user, token, createdAt
                $reset_pw = new ResetPassword();   
                $reset_pw->setUser($user);
                $reset_pw->setToken(uniqid());        
                $reset_pw->setCreatedAt(new \DateTime());
                $mananger->persist($reset_pw);
                $mananger->flush();     

                //send mail to user with link to update pw
                $mail = new MailService();
                $url = $this->generateUrl('update_password', [
                    'token' => $reset_pw->getToken()
                ]);

                $content = "Hello ".$user->getNickname(). "<br> You need to reset your pw ! </br>";
                $content .= "Follow the link <a href='".$url."'> Reset Password </a>.";

                $mail->send($user->getEmail(), $user->getNickName(), 'Change your pw', $content);

                $this->addFlash('success', "A mail has been send to your mailbox to reset pw");
            }else{
                $this->addFlash('error', "this address does't exist");
            }
        }
        return $this->render('reset_password/index.html.twig', [
            
        ]);
    }

      /**
     * @Route("/update-password/{token}", name="update_password")
     */
    public function updatePw($token, ResetPasswordRepository $resetPasswordRepo, 
                             UserPasswordEncoderInterface $encoder, 
                             EntityManagerInterface $manager, Request $request): Response
    {
        $reset_password = $resetPasswordRepo->findOneByToken($token);
        if (!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }else{
            //check if createdAt = now - 3h
            $now = new \DateTime();
            if ($now > $reset_password->getCreatedAt()->modify('+ 3 hours')) {
                $this->addFlash('notice', 'New password request expired, you may try again');
                return $this->redirectToRoute('reset_password');
            }

            // return a viw with new pw & confirm
            $form = $this->createForm(ResetPasswordType::class);
            $user = $this->getUser();
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $new_pwd = $form->get('newPassword')->getData();
                $password = $encoder->encodePassword($reset_password->getUser(), $new_pwd);
                $reset_password->getUser()->setPassword($password);
                $manager->flush();

                $this->addFlash('success', 'your password has been reset');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('reset_password/update.html.twig', [
                'form' => $form->createView()
            ]);

         
        }
    }
}
