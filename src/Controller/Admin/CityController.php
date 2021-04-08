<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use App\Repository\PictureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/city')]
class CityController extends AbstractController
{
    /**
     * @param CityRepository $cityRepository
     * @return Response
     */
    #[Route('/', name: 'admin_city_index')]
    public function index(CityRepository $cityRepository): Response
    {
        return $this->render('Admin/city/index.html.twig', [
            'cities' => $cityRepository->findAll(),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route('/new',name: 'admin_city_new', methods: ["GET","POST"])]
    public function new(Request $request): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('admin_city_index');
        }

        return $this->render('Admin/city/new.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param City $city
     * @param PictureRepository $pictureRepository
     * @return Response
     */
    #[Route('/{id}',name: 'admin_city_show', methods: ["GET"])]
    public function show(City $city, PictureRepository $pictureRepository): Response
    {
        $pictures = $pictureRepository->findByCity($city);
        return $this->render('Admin/city/show.html.twig', [
            "pictures" => $pictures,
            'city' => $city,
        ]);
    }

    /**
     * @param Request $request
     * @param City $city
     * @return Response
     */
    #[Route('/{id}/edit',name: 'admin_city_edit', methods: ["GET","POST"])]
    public function edit(Request $request, City $city): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_city_index');
        }

        return $this->render('Admin/city/edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param City $city
     * @return Response
     */
    #[Route('/{id}',name: 'admin_city_delete', methods: ["DELETE"])]
    public function delete(Request $request, City $city): Response
    {
        if ($this->isCsrfTokenValid('<delete'.$city->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($city);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_city_index');
    }
}
