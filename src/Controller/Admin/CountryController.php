<?php

namespace App\Controller\Admin;

use App\Entity\Country;
use App\Form\CountryType;
use App\Repository\CityRepository;
use App\Repository\CountryRepository;
use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/country')]
class CountryController extends AbstractController
{
    /**
     * @param CountryRepository $countryRepository
     * @return Response
     */
    #[Route('/',name: 'admin_country_index', methods: ["GET"])]
    public function index(CountryRepository $countryRepository): Response
    {
        return $this->render('Admin/country/index.html.twig', [
            'countries' => $countryRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/new',name: 'admin_country_new', methods: ["GET","POST"])]
    public function new(Request $request): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($country);
            $entityManager->flush();

            return $this->redirectToRoute('admin_country_index');
        }

        return $this->render('Admin/country/new.html.twig', [
            'country' => $country,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Country $country
     * @param PictureRepository $pictureRepository
     * @return Response
     */
    #[Route('/{id}',name: 'admin_country_show', methods: ["GET"])]
    public function show(Country $country, PictureRepository $pictureRepository): Response
    {
        $pictures = $pictureRepository->findByCountry($country);
        return $this->render('Admin/country/show.html.twig', [
            'country' => $country,
            "pictures" => $pictures
        ]);
    }

    /**
     * @param Request $request
     * @param Country $country
     * @return Response
     */
    #[Route('/{id}/edit',name: 'admin_country_edit', methods: ["GET","POST"])]
    public function edit(Request $request, Country $country): Response
    {
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_country_index');
        }

        return $this->render('Admin/country/edit.html.twig', [
            'country' => $country,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param Country $country
     * @return Response
     */
    #[Route('/{id}',name: 'admin_country_delete', methods: ["DELETE"])]
    public function delete(Request $request, Country $country): Response
    {
        if ($this->isCsrfTokenValid('delete'.$country->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($country);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_country_index');
    }
}
