<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserEditType;
use App\Form\UserType;
use App\Services\MailService;
use App\Services\NotificationServices;
use DateTime;
use Exception;
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
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    #[Route('/login', name: 'app_login')]
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
     * @param MailService $mailService
     * @return RedirectResponse|Response
     * @throws Exception
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request,NotificationServices $notificationServices, TranslatorInterface $translator, UserPasswordEncoderInterface $encoder, MailService $mailService): RedirectResponse|Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $encoder->encodePassword($user, $user->getPassword());
            $user->setCreatedAt(new DateTime());
            $user->setRoles(["ROLE_USER"]);
            $user->setStatus(0);
            $user->setPassword($hash);
            $em->persist($user);
            $em->flush();
            $message = $translator->trans("flashes.email.firstStapeSuccessfully", ["%firstname%" => $user->getFirstname(), "%lastname%" => $user->getLastname()], "FlashesMessages");
            $this->addFlash("success", $message);
            $subject = $translator->trans("email.subject.subscribe", [], "OurTripsTrans");
            $mailService->sendMail($subject, $user->getEmail(), ["subscribe" => true, "user" => $user]);
            $notifContent = $translator->trans("notification.newMember", [], "OurTripsTrans");
            $notificationServices->createNotification($notifContent, ["app_show", $user->getId()]);
            $em->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render("security/register.html.twig", [
            "form" => $form->createView()
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     * @param TranslatorInterface $translator
     * @return Response
     */
    #[Route('/edit/{id}', name: 'app_edit')]
    public function edit(User $user, Request $request, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $user->setPassword($user->getPassword());
            $em->persist($user);
            $em->flush();
            $this->addFlash("success", $translator->trans("flashes.user.modifyProfile", ["%firstname%" => $user->getFirstname()], "FlashesMessages"));
            return $this->redirectToRoute("app_show", ["id" => $user->getId()]);
        }
        return $this->render("security/edit.html.twig", [
            "user" => $user,
            "form" => $form->createView()
        ]);
    }

    /**
     * @param User $user
     * @return Response
     */
    #[Route('/show/{id}', name: 'app_show')]
    public function show(User $user): Response
    {
        return $this->render("security/profile.html.twig", [
            "user" => $user
        ]);
    }

    /**
     * @param User $user
     * @param TranslatorInterface $translator
     * @param NotificationServices $notificationServices
     * @return RedirectResponse
     * @throws Exception
     */
    #[Route('/change-status/{id}', name: 'user_change_status')]
    public function changeStatus(User $user, TranslatorInterface $translator, NotificationServices $notificationServices): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        $user->setStatus(1);
        $em->persist($user);
        $message = $translator->trans("flashes.email.confirmed", ["%firstname%" => $user->getFirstname(), "%lastname%" => $user->getLastname()], "FlashesMessages");
        $this->addFlash("success", $message);
        $notifContent = $translator->trans("notification.newMember", [], "OurTripsTrans");
        $notificationServices->createNotification($notifContent, ["app_show", $user->getId()]);
        $em->flush();
        return $this->redirectToRoute("home");
    }

    /**
     * @param User $user
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param MailService $mailService
     * @return RedirectResponse|Response
     */
    #[Route('/change-password/{id}', name: 'user_change_password')]
    public function changePassword(User $user, Request $request, TranslatorInterface $translator, MailService $mailService)
    {
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $translator->trans("flashes.email.firstStapeChangePassword", ["%firstname%" => $user->getFirstname(), "%lastname%" => $user->getLastname()], "FlashesMessages");
            $this->addFlash("success", $message);
            $subject = $translator->trans("email.subject.changePassword", [], "OurTripsTrans");
            $mailService->sendMail($subject, $user->getEmail(), ["changePassword" => true, "user" => $user]);
            return $this->redirectToRoute("home");
        }
        return $this->render("security/changePassword.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
