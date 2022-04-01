<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController {


    /**
     * @Route("/login", name="ec_login")
     */
    public function login (AuthenticationUtils $authenticationUtils): Response {

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('Authentication/login.html.twig',[
            'error' => $error,
            'lastUsername' => $lastUsername
        ]);

    }


    /**
     * @Route("/register", name="ec_register")
     */
    public function register (Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class,$user);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ) {
            $user->setPassword(
                $userPasswordHasher->hashPassword($user, $form->get('password')->getData())
            );
            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush($user);
            
            return $this->redirectToRoute('home');
        }

        return $this->render('Authentication/register.html.twig',[
            'form'=> $form->createView()
        ]);
    }


    /**
     * @Route("/logout", name="ec_logout")
     */
    public function logout (): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }




}