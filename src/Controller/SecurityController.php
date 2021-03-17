<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param UserPasswordEncoderInterface $encoder
     * @return RedirectResponse|Response
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, TranslatorInterface $translator, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setRoles(["ROLE_USER"]);
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();
            $message = $translator->trans("flashes.user.firstStapeSuccessfully", ["%firstname%" => $user->getFirstname(), "%lastname%" => $user->getLastname()], "FlashesMessages");
            $this->addFlash("success", $message);
            return $this->redirectToRoute("home");
        }
        return $this->render("security/register.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
