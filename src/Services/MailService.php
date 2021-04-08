<?php


namespace App\Services;


use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class MailService
{
    /**
     * @var MailerInterface
     */
    private MailerInterface $mailer;
    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(MailerInterface $mailer, UserRepository $userRepository)
    {
        $this->mailer = $mailer;
        $this->userRepository = $userRepository;
    }

    /**
     * @param String $subject
     * @param $receiver
     * @param array $context []
     */
    public function sendMail(string $subject, $receiver, $context = [])
    {
        //creation of an email
        $email = (new TemplatedEmail())
            ->from("sl0ders@gmail.com")
            ->to($receiver)
            ->replyTo("sl0ders@gmail.com")
            ->subject($subject)
            ->htmlTemplate("Email/emailConfirme.html.twig")
            ->context($context);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            echo 'Exception reçue : ', $e->getMessage(), "\n";
        }
    }
}
