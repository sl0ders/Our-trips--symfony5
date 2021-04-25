<?php


namespace App\Controller;


use App\Entity\City;
use App\Entity\Comment;
use App\Entity\Country;
use App\Entity\Picture;
use App\Entity\SearchData;
use App\Form\CommentType;
use App\Form\SearchFormType;
use App\Repository\CommentRepository;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use App\Services\NotificationServices;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PictureController
 * @package App\Controller\Public
 */
#[Route('/picture')]
class PictureController extends AbstractController
{
    /**
     * @param PictureRepository $pictureRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'public_picture_index', methods: ["GET"])]
    public function index(PictureRepository $pictureRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        $pictures = $pictureRepository->findSearch($data);
        $pagination = $paginator->paginate(
            $pictures,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('Public/picture/index.html.twig', [
            'form' => $form->createView(),
            'pagination' => $pagination
        ]);
    }

    /**
     * @param Picture $picture
     * @param Request $request
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param TranslatorInterface $translator
     * @param CommentRepository $commentRepository
     * @param NotificationServices $notificationServices
     * @return Response
     * @throws \Exception
     */
    #[Route('/show/{id}', name: 'public_picture_show', methods: ["GET", "POST"])]
    public function show(Picture $picture, Request $request, UserRepository $userRepository, PaginatorInterface $paginator, TranslatorInterface $translator, CommentRepository $commentRepository, NotificationServices $notificationServices): Response
    {
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);
        $user = $userRepository->find($this->getUser()->getId());
        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment->setCreatedAt(new \DateTime());
            $comment->setEnabled(false);
            $comment->setLevel(1);
            $comment->setAuthor($user);
            $comment->setPicture($picture);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $notifContent = $translator->trans("notification.newComment", ["%date%" => $comment->getCreatedAt()->format("d/m/Y")], "OurTripsTrans");
            $notificationServices->createNotification($notifContent, ["admin_comment_show", $comment->getId()]);
            $em->flush();
            $this->addFlash("success", $translator->trans("comment.stand", ["%firstname%" => $user->getFirstname()], "OurTripsTrans"));
            return $this->redirectToRoute("public_picture_show", ["id" => $picture->getId()]);
        }
        $comments = $commentRepository->findBy(["enabled" => true, "picture" => $picture]);
        $pagination = $paginator->paginate(
            $comments,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render("Admin/picture/show.html.twig", [
            "picture" => $picture,
            "form_comment" => $formComment->createView(),
            "comments" => $pagination
        ]);
    }
}
