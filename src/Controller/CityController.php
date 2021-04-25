<?php

namespace App\Controller;

use App\Entity\City;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/city')]
class CityController extends AbstractController
{
    #[Route('/{id}', name: 'public_city_index', requirements: ["id" => "\d+"])]
    public function index(City $city, PaginatorInterface $paginator, Request $request): Response
    {
        $pictures = $city->getPictures();
        $pagination = $paginator->paginate(
            $pictures,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('city/index.html.twig', [
            'pagination' => $pagination,
            "city" => $city
        ]);
    }
}
