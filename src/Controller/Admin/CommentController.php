<?php

namespace App\Controller\Admin;

use App\Datatable\CommentDatatable;
use App\Datatable\NewsDatatable;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\NewsRepository;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/admin/comment')]
class CommentController extends AbstractController
{
    /**
     * @var DatatableFactory
     */
    private DatatableFactory $factory;

    /**
     * @var DatatableResponse
     */
    private DatatableResponse $response;

    public function __construct(DatatableFactory $factory, DatatableResponse $response)
    {
        $this->factory = $factory;
        $this->response = $response;
    }

    /**
     * @param CommentRepository $newsRepository
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    #[Route('/',name: 'admin_comment_index', methods: ["GET"])]
    public function index(CommentRepository $newsRepository, Request $request): Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->factory->create(CommentDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->response;
            $responseService->setDatatable($datatable);
            $responseService->getDatatableQueryBuilder();

            return $responseService->getResponse();
        }
        return $this->render('Admin/comment/index.html.twig', [
            'comment' => $newsRepository->findAll(),
            "datatable" => $datatable
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/new',name: 'admin_comment_new', methods: ["GET","POST"])]
    public function new(Request $request): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('admin_comment_index');
        }

        return $this->render('Admin/comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Comment $comment
     * @return Response
     */
    #[Route('/{id}',name: 'admin_comment_show', methods: ["GET"])]
    public function show(Comment $comment): Response
    {
        return $this->render('Admin/comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * @param Request $request
     * @param Comment $comment
     * @return Response
     */
    #[Route('/{id}',name: 'admin_comment_delete', methods: ["DELETE"])]
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_comment_index');
    }
}
