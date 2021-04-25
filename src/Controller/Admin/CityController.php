<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Picture;
use App\Form\CityType;
use App\Repository\CityRepository;
use App\Repository\PictureRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
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
     * @param UserRepository $userRepository
     * @return Response
     * @throws Exception
     */
    #[Route('/new', name: 'admin_city_new', methods: ["GET", "POST"])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
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
    #[Route('/{id}', name: 'admin_city_show', methods: ["GET"])]
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
     * @param UserRepository $userRepository
     * @return Response
     * @throws Exception
     */
    #[Route('/{id}/edit', name: 'admin_city_edit', methods: ["GET", "POST"])]
    public function edit(Request $request, City $city, UserRepository $userRepository): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);
        $entityManager = $this->getDoctrine()->getManager();
        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve the pictures
            $files = $form["pictures"]->getData();
            if (isset($files)) {
                foreach ($files as $file) {
                    $picture_info = exif_read_data($file->getLinkTarget());
                    $fileName = ($file->getClientOriginalName());
                    $file->move(
                        $this->getParameter("image_directory"),
                        $fileName
                    );
                    $picture = new Picture();
                    if (isset($picture_info["Make"])) {
                        if ($picture_info["Make"] == "iphone") {
                            $userRepository->findBy(["firstname" => "Anne-charlotte"]);
                            $author = $userRepository->findOneBy(["firstname" => "Anne charlotte"]);
                            $picture->setAuthor($author);
                        } else {
                            $author = $userRepository->findOneBy(["firstname" => "Quentin"]);
                            $picture->setAuthor($author);
                        }
                    }
                    if (isset($picture_info['DateTimeOriginal'])) {
                        $createdAt = new DateTime($picture_info['DateTimeOriginal']);
                    } else {
                        $createdAt = new DateTime();
                    }
                    if (isset($picture_info['ImageWidth']) && isset($picture_info['ImageLength'])) {
                        $dimension = [$picture_info['ImageWidth'] . "," . $picture_info['ImageLength']];
                        $picture->getPicture()->setDimensions($dimension);
                    }
                    $picture->setCity($city);
                    $picture->setPostAt(new DateTime());
                    $picture->setCreatedAt($createdAt);
                    $picture->getPicture()->setName($fileName);
                    $picture->getPicture()->setOriginalName($file->getClientOriginalName());
                    $picture->getPicture()->setMimeType($picture_info["MimeType"]);
                    $picture->getPicture()->setSize($picture_info["FileSize"]);
                    $city->addPicture($picture);
                }
            }

            $entityManager->persist($city);
            $entityManager->flush();

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
    #[Route('/{id}', name: 'admin_city_delete', methods: ["DELETE"])]
    public function delete(Request $request, City $city): Response
    {
        if ($this->isCsrfTokenValid('delete' . $city->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($city);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_city_index');
    }
}
