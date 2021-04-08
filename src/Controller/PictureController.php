<?php


namespace App\Controller;


use App\Entity\City;
use App\Entity\Comment;
use App\Entity\Country;
use App\Entity\Picture;
use App\Entity\SearchData;
use App\Form\CommentType;
use App\Form\SearchFormType;
use App\Repository\CityRepository;
use App\Repository\CommentRepository;
use App\Repository\PictureRepository;
use App\Repository\SearchDataRepository;
use App\Repository\UserRepository;
use DateTime;
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
     * @param City $city
     * @param PictureRepository $pictureRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/index/city/{id}', name: 'public_picture_index_city', methods: ["GET"])]
    public function indexCity(City $city, PictureRepository $pictureRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pictures = $pictureRepository->findBy(["city" => $city]);
        $pagination = $paginator->paginate(
            $pictures,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render("Public/picture/city.html.twig", [
            "city" => $city,
            "pictures" => $pictures,
            'pagination' => $pagination
        ]);
    }

    /**
     * @param Country $country
     * @param PictureRepository $pictureRepository
     * @param Request $request
     * @param PaginatorInterface $paginator
     * @return Response
     */
    #[Route('/index/country/{id}', name: 'public_picture_index_country', methods: ["GET"])]
    public function indexCountry(Country $country, PictureRepository $pictureRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pictures = $pictureRepository->findByCountry($country);
        $pagination = $paginator->paginate(
            $pictures,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render("Public/picture/country.html.twig", [
            "country" => $country,
            "pagination" => $pagination,
        ]);
    }

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
     * @return Response
     */
    #[Route('/show/{id}', name: 'public_picture_show', methods: ["GET", "POST"])]
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
            return  $this->redirectToRoute("public_picture_show", ["id" => $picture->getId()]);
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
