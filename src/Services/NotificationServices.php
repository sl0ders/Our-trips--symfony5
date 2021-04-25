<?php


namespace App\Services;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class NotificationServices
{
    private NotificationRepository $notificationRepository;
    private EntityManagerInterface $em;

    public function __construct(NotificationRepository $notificationRepository, EntityManagerInterface $em)
    {
        $this->notificationRepository = $notificationRepository;
        $this->em = $em;
    }

    /**
     * @param $content
     * @param array $path
     * @throws Exception
     */
    public function createNotification($content, $path = [] ) {
        $date = new DateTime();
        // This date is the time limit before filing the notification
        $dateM = $date->modify("+6 month");
        $datesync = new DateTime();
        $datesync = $datesync->format("d-m-Y H:i:s");
        $dateFinal = new DateTime($datesync);

        // we check if this notification exists, and if it does not exist we create a new one
        $notification = $this->notificationRepository->findOneBy(['created_at' => $date]);
        if (!isset($notification)) {
            $notification = new Notification();
            $notification->setIsRead(false);
            $notification
                ->setCreatedAt($dateFinal)
                ->setContent($content)
                ->setExpirationDate($dateM);
            if ($path) {
                //the first parameter of option is the path name and the second is the id
                $notification->setPath($path[0]);
                $notification->setIdPath($path[1]);
            }
          $this->em->persist($notification);
        }
        return $notification;
    }
}
