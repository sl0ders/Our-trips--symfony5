<?php


namespace App\Controller;


use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\NewsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $news = $newsRepository->findBy([], ["created_at" => "DESC"], "6");
        return $this->render('Layout/_rightLayout.html.twig', [
            'news' => $news,
        ]);
    }

    public function footerAction(): Response
    {
        return $this->render('Layout/_footer.html.twig', [
        ]);
    }

    /**
     * @Route("/find-city", name="find_city", methods={"GET"}, options={"explose" = true})
     * @param Request $request
     * @param CountryRepository $countryRepository
     * @return Response
     */
    public function findCity(Request $request, CountryRepository $countryRepository): Response
    {
        $countryId = $request->query->get("country");
        $country = $countryRepository->find($countryId);
        $citys = [];
        $cities = $country->getCities();
        foreach ($cities as $city) {
            $citys[$city->getId()] = $city->getid();
        }
        $response = new Response(json_encode($citys));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }
}
