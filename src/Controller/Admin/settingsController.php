<?php


namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class settingsController
 * @package App\Controller\Admin
 * @Route("/admin")
 */
class settingsController extends AbstractController
{
    /**
     * @Route("/settings", name="admin_settings_index")
     */
    public function index(): Response
    {
        return $this->render("Admin/settings.html.twig");
    }
}
