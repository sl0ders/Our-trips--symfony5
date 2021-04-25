<?php

namespace App\Controller\Admin;

use App\Datatable\CommentDatatable;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
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
     * @throws Exception
     */
    #[Route('/', name: 'admin_comment_index', methods: ["GET"])]
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
     * @param Comment $comment
     * @return Response
     */
    #[Route('/{id}', name: 'admin_comment_show', methods: ["GET"])]
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
    #[Route('/{id}', name: 'admin_comment_delete', methods: ["DELETE"])]
    public function delete(Request $request, Comment $comment): Response
    {
        if ($this->isCsrfTokenValid('delete' . $comment->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($comment);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_comment_index');
    }

    /**
     * @param Comment $comment
     * @param TranslatorInterface $translator
     * @return RedirectResponse
     */
    #[Route('/enabled/{id}', name: 'admin_comment_enabled', methods: ["POST", "GET"])]
    public function enabled(Comment $comment, TranslatorInterface $translator)
    {
        $messageEnabled = $translator->trans("flashes.comment.enabled", [], "FlashesMessages");
        $messageDisabled = $translator->trans("flashes.comment.disabled", [], "FlashesMessages");
        if ($comment->getEnabled() == false) {
            $comment->setEnabled(true);
            $this->addFlash("success", $messageEnabled );
        } else {
            $comment->setEnabled(false);
            $this->addFlash("success", $messageDisabled );
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($comment);
        $em->flush();
        return $this->redirectToRoute("admin_comment_index");
    }
}
