<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\SearchData;
use App\Form\CommentType;
use App\Form\FolderType;
use App\Form\PictureType;
use App\Form\SearchFormType;
use App\Repository\CommentRepository;
use App\Repository\NewsRepository;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/picture')]
class PictureController extends AbstractController
{
    /**
     * @param PictureRepository $pictureRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'admin_picture_index', methods: ["GET"])]
    public function index(PictureRepository $pictureRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $data = new SearchData();
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        $pictures = $pictureRepository->findSearch($data);
        $pagination = $paginator->paginate(
            $pictures,
            $request->query->getInt('page', 1),
            15
        );
        return $this->render('Admin/picture/index.html.twig', [
            'pagination' => $pagination,
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    #[Route('/new', name: 'admin_picture_new', methods: ["GET", "POST"])]
    public function new(Request $request): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $picture_info = exif_read_data($form["pictureFile"]->getData()->getLinkTarget());

            if (isset($picture_info['DateTimeOriginal'])) {
                $createdAt = new DateTime($picture_info['DateTimeOriginal']);
            } else {
                $createdAt = new DateTime();
            }
            $picture->setPostAt(new DateTime());
            $picture->setCreatedAt($createdAt);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($picture);
            if ($form['newCity']->getData()->getName() != null) {
                /** @var City $city */
                $city = $form['newCity']->getData();
                $entityManager->persist($city);
                $picture->setCity($form['newCity']->getData());
                $entityManager->persist($city);
                $entityManager->persist($picture);
                $entityManager->flush();
            }
            $entityManager->flush();
            return $this->redirectToRoute('admin_picture_index');
        }
        return $this->render('Admin/picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Picture $picture
     * @param Request $request
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @param TranslatorInterface $translator
     * @param CommentRepository $commentRepository
     * @return Response
     */
    #[Route('/{id}', name: 'admin_picture_show', requirements: ["id" => "\d+"], methods: ["GET", "POST"])]
    public function show(Picture $picture, Request $request, UserRepository $userRepository, PaginatorInterface $paginator, TranslatorInterface $translator, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);
        $user = $userRepository->find($this->getUser()->getId());
        if ($formComment->isSubmitted() && $formComment->isValid()) {
            $comment->setCreatedAt(new DateTime());
            $comment->setEnabled(false);
            $comment->setLevel(1);
            $comment->setAuthor($user);
            $comment->setPicture($picture);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();
            $this->addFlash("success", $translator->trans("comment.stand", ["%firstname%" => $user->getFirstname()], "OurTripsTrans"));
            return $this->redirectToRoute("admin_picture_show", ["id" => $picture->getId()]);
        }
        $comments = $commentRepository->findBy(["enabled" => true, "picture" => $picture]);
        $pagination = $paginator->paginate(
            $comments,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('Admin/picture/show.html.twig', [
            'picture' => $picture,
            "form_comment" => $formComment->createView(),
            "comments" => $pagination
        ]);
    }

    /**
     * @param Request $request
     * @param Picture $picture
     * @return Response
     */
    #[Route('/{id}/edit', name: 'admin_picture_edit', methods: ["GET", "POST"])]
    public function edit(Request $request, Picture $picture): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_picture_index');
        }

        return $this->render('Admin/picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form->createView(),
        ]);
    }



    /**
     * @param Request $request
     * @param Picture $picture
     * @param NewsRepository $newsRepository
     * @return Response
     */
    #[Route('/{id}', name: 'admin_picture_delete', methods: ["DELETE"])]
    public function delete(Request $request, Picture $picture, NewsRepository $newsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $picture->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $new = $newsRepository->findOneBy(["link" => $picture->getId()]);
            if (isset($new)) {
                $entityManager->remove($new);
            }
            $entityManager->remove($picture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_picture_index');
    }

    /**
     * @param City $city
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     * @throws Exception
     */
    #[Route('/addPictures/{id}', name: 'admin_picture_addFolder', methods: ["GET", "POST"])]
    public function addPictures(City $city, Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(FolderType::class);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form["pictures"]->getData();
            foreach ($files as $file) {
                $picture_info = exif_read_data($file->getLinkTarget());
                $fileName = ($file->getClientOriginalName());
                $file->move(
                    $this->getParameter("image_directory"),
                    $fileName
                );
                $picture = new Picture();
                if (isset($picture_info["Make"])) {
                    if ($picture_info["Make"] == "iphone") {
                        $userRepository->findBy(["firstname" => "Anne-charlotte"]);
                        $author = $userRepository->findOneBy(["firstname" => "Anne charlotte"]);
                        $picture->setAuthor($author);
                    } else {
                        $author = $userRepository->findOneBy(["firstname" => "Quentin"]);
                        $picture->setAuthor($author);
                    }
                }
                if (isset($picture_info['DateTimeOriginal'])) {
                    $createdAt = new DateTime($picture_info['DateTimeOriginal']);
                } else {
                    $createdAt = new DateTime();
                }
                if (isset($picture_info['ImageWidth']) && isset($picture_info['ImageLength'])) {
                    $dimension = [$picture_info['ImageWidth'] . "," . $picture_info['ImageLength']];
                    $picture->getPicture()->setDimensions($dimension);
                }
                $picture->setPostAt(new DateTime());
                $picture->setCreatedAt($createdAt);
                $picture->getPicture()->setName($fileName);
                $picture->getPicture()->setOriginalName($file->getClientOriginalName());
                $picture->getPicture()->setMimeType($picture_info["MimeType"]);
                $picture->getPicture()->setSize($picture_info["FileSize"]);
                $picture->setCity($city);
                $em->persist($picture);
            }
            $em->flush();
            return $this->redirectToRoute('admin_picture_index');
        }
        return $this->render("Admin/picture/newsFolder.html.twig", [
            "form" => $form->createView()
        ]);
    }

    #[Route("/delete-multiple", name: "admin_picture_deletemultiple")]
    public function deleteMultiple(Request $request, PictureRepository $pictureRepository): Response
    {
        $pictures = $request->request->get("pictures");
        $images = [];
        $em = $this->getDoctrine()->getManager();
        foreach ($pictures as $picture) {
            $pictureObj = $pictureRepository->find($picture);
            array_push($images, $pictureObj);
            $em->remove($pictureObj);
            $em->flush();
        }

        $response = new Response(json_encode($pictures));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
