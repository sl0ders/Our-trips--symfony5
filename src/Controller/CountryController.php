<?php

namespace App\Controller;

use App\Entity\Country;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/country')]
class CountryController extends AbstractController
{
    #[Route('/{id}', name: 'public_country_index', requirements: ['id' => '\d+'])]
    public function index(Country $country): Response
    {
        $cities = $country->getCities();
        return $this->render('country/index.html.twig', [
            'cities' => $cities,
            "country" => $country
        ]);
    }
}
