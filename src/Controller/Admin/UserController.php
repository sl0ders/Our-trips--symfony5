<?php

namespace App\Controller\Admin;

use App\Datatable\newsDatatable;
use App\Datatable\UserDatatable;
use App\Entity\Notification;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Services\NotificationServices;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/user')]
class UserController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    #[Route('/', name: 'admin_user_index', methods: ["GET"])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        return $this->render('Admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @param TranslatorInterface $translator
     * @param NotificationServices $notificationServices
     * @return Response
     * @throws Exception
     */
    #[Route('/new', name: 'admin_user_new', methods: ["GET", "POST"])]
    public function new(Request $request, TranslatorInterface $translator, NotificationServices $notificationServices): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $message = $translator->trans("notification.newMember", [], 'OurTripsTrans');
            $notificationServices->createNotification($message, ["admin_user_index"]);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('Admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param User $user
     * @return Response
     */
    #[Route('/{id}', name: 'admin_user_show', methods: ["GET"])]
    public function show(User $user): Response
    {
        return $this->render('Admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @param User $user
     * @param TranslatorInterface $translator
     * @return RedirectResponse
     */
    #[Route("block/{id}", name: "admin_user_block", requirements: ["id" => '\d+'])]
    public function blockUser(User $user, TranslatorInterface $translator): RedirectResponse
    {
        $user->setStatus(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
        $this->addFlash("success", $translator->trans("user.status.blocked", [], "OurTripsTrans"));
        return $this->redirectToRoute("admin_user_index");
    }

    /**
     * @param Request $request
     * @param User $user
     * @return Response
     */
    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ["GET", "POST"])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('Admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_user_delete", methods={"DELETE"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    #[Route('/{id}', name: 'admin_user_delete', methods: ["DELETE"])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
