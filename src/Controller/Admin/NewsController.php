<?php

namespace App\Controller\Admin;

use App\Datatable\NewsDatatable;
use App\Entity\News;
use App\Entity\Picture;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use App\Repository\UserRepository;
use DateTime;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Datatable\Factory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/news')]
class NewsController extends AbstractController
{
    /**
     * @var Factory
     */
    private $factory;
    /**
     * @var DatatableResponse
     */
    private $response;

    public function __construct(DatatableFactory $factory, DatatableResponse $response)
    {
        $this->factory = $factory;
        $this->response = $response;
    }

    /**
     * @param NewsRepository $newsRepository
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    #[Route('/',name: 'admin_news_index', methods: ["GET"])]
    public function index(NewsRepository $newsRepository, Request $request): Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->factory->create(NewsDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->response;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }
        return $this->render('Admin/news/index.html.twig', [
            'news' => $newsRepository->findAll(),
            "datatable" => $datatable
        ]);
    }

    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @return Response
     */
    #[Route('/new',name: 'admin_news_new', methods: ["GET","POST"])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $newPicture = $form['addPicture']->getData();
            if ($newPicture->getPictureFile() != null) {
                /** @var Picture $newPicture */
                $newPicture = $form['addPicture']->getData();
                $newPicture->setCreatedAt(new DateTime());
                $em->persist($newPicture);
                $news->setLink($newPicture);
            }
            $author = $this->getUser();
            $user = $userRepository->find($author->getId());
            $entityManager = $this->getDoctrine()->getManager();
            $news->setAuthor($user);
            $news->setCreatedAt(new DateTime());
            $entityManager->persist($news);
            $entityManager->flush();

            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('Admin/news/new.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param News $news
     * @return Response
     */
    #[Route('/{id}',name: 'admin_news_show', methods: ["GET"])]
    public function show(News $news): Response
    {
        return $this->render('Admin/news/show.html.twig', [
            'news' => $news,
        ]);
    }

    /**
     * @param Request $request
     * @param News $news
     * @return Response
     */
    #[Route('/{id}/edit',name: 'admin_news_edit', methods: ["GET","POST"])]
    public function edit(Request $request, News $news): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            $newPicture = $form['addPicture']->getData();
            if ($newPicture->getPictureFile() != null) {
                /** @var Picture $newPicture */
                $newPicture = $form['addPicture']->getData();
                $newPicture->setCreatedAt(new DateTime());
                $em->persist($newPicture);
                $news->setLink($newPicture);
                $em->persist($news);
            }
            $em->flush();
            return $this->redirectToRoute('admin_news_index');
        }

        return $this->render('Admin/news/edit.html.twig', [
            'news' => $news,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_news_delete", methods={"DELETE"})
     * @param Request $request
     * @param News $news
     * @return Response
     */
    #[Route('/{id}',name: 'admin_news_delete', methods: ["DELETE"])]
    public function delete(Request $request, News $news): Response
    {
        if ($this->isCsrfTokenValid('delete' . $news->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($news);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_news_index');
    }
}
