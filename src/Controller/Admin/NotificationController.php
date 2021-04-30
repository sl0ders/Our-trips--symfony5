<?php

namespace App\Controller\Admin;

use App\Datatable\NotificationDatatable;
use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Exception;
use Sg\DatatablesBundle\Datatable\DatatableFactory;
use Sg\DatatablesBundle\Response\DatatableResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/notification')]
class NotificationController extends AbstractController
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
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    #[Route('/', name: 'admin_notification_index')]
    public function index(Request $request): Response
    {
        $isAjax = $request->isXmlHttpRequest();
        $datatable = $this->factory->create(NotificationDatatable::class);
        $datatable->buildDatatable();

        if ($isAjax) {
            $responseService = $this->response;
            $responseService->setDatatable($datatable);
            $datatableQueryBuilder = $responseService->getDatatableQueryBuilder();
            $qb = $datatableQueryBuilder->getQb();
            $qb
                ->orderBy("notification.isRead", "ASC");
            return $responseService->getResponse();
        }
        return $this->render('Admin/notification/index.html.twig', [
            'datatable' => $datatable,
        ]);
    }

    #[Route('/{id}', name: 'admin_notification_show')]
    public function show(Notification $notification): Response
    {
        $em = $this->getDoctrine()->getManager();
        if ($notification->getIsRead() == false) {
            $notification->setIsRead(true);
        }
        $em->persist($notification);
        $em->flush();
        return $this->render('Admin/notification/show.html.twig', [
            'notification' => $notification,
        ]);
    }

    /**
     * @param Notification $notification
     * @return RedirectResponse
     */
    #[Route('read/{id}', name: 'admin_notification_read')]
    public function read(Notification $notification): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();
        if ($notification->getIsRead() == true) {
            $notification->setIsRead(false);
        } else if ($notification->getIsRead() == false) {
            $notification->setIsRead(true);
        }
        $em->persist($notification);
        $em->flush();
        return $this->redirectToRoute("admin_notification_index");
    }

    #[Route('changeRead', name: "admin_change_read", options: ["explose" => true], methods: ['GET'])]
    public function changeRead(NotificationRepository $notificationRepository, Request $request): Response
    {
        $notificationId = $request->get("notification");
        $notification = $notificationRepository->find($notificationId);
        if ($notification->getIsRead() === false) {
            $notification->setIsRead(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($notification);
            $em->flush();
            $notif["idPath"] = $notification->getIdPath();
            $notif['path'] = $notification->getPath();
            $response = new Response(json_encode($notif));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            return $this->redirectToRoute($notification->getPath(), ["id" => $notification->getIdPath()]);
        }
    }
}
