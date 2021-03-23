<?php


namespace App\Controller;


use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class LayoutBundleController extends AbstractController
{
    public function headerAction(): Response
    {
        return $this->render('Layout/_header.html.twig', [
        ]);
    }

    public function leftMenuAction(CountryRepository $countryRepository): Response
    {
        $countries = $countryRepository->findAll();
        return $this->render('Layout/_leftLayout.html.twig', [
            'countries' => $countries
        ]);
    }

    public function rightMenuAction(CityRepository $cityRepository, NewsRepository $newsRepository): Response
    {
        $news = $newsRepository->findAll();
        return $this->render('Layout/_rightLayout.html.twig', [
            'news' => $news,
        ]);
    }

    public function footerAction(): Response
    {
        return $this->render('Layout/_footer.html.twig', [
        ]);
    }
}
