<?php

namespace App\Controller\Admin;

use App\Entity\City;
use App\Entity\Picture;
use App\Form\PictureType;
use App\Repository\PictureRepository;
use DateTime;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/picture")
 */
class PictureController extends AbstractController
{
    /**
     * @Route("/", name="admin_picture_index", methods={"GET"})
     * @param PictureRepository $pictureRepository
     * @return Response
     */
    public function index(PictureRepository $pictureRepository): Response
    {
        $pictures = $pictureRepository->findAll();
        return $this->render('Admin/picture/index.html.twig', [
            'pictures' => $pictures,
        ]);
    }

    /**
     * @Route("/new", name="admin_picture_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function new(Request $request): Response
    {
        $picture = new Picture();
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $picture_info = exif_read_data($form["pictureFile"]->getData()->getLinkTarget());
            $createdAt = new DateTime($picture_info['DateTimeOriginal']);
            $picture->setPostAt(new DateTime());
            $picture->setCreatedAt($createdAt);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($picture);
            if ($form['newCity']->getData()->getName() != null) {
                /** @var City $city */
                $city = $form['newCity']->getData();
                $entityManager->persist($city);
                $picture->setCity($form['newCity']->getData());
                $entityManager->persist($picture);
            }
            $entityManager->flush();
            return $this->redirectToRoute('admin_picture_index');
        }

        return $this->render('Admin/picture/new.html.twig', [
            'picture' => $picture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_picture_show", methods={"GET"})
     * @param Picture $picture
     * @return Response
     */
    public function show(Picture $picture): Response
    {
        return $this->render('Admin/picture/show.html.twig', [
            'picture' => $picture,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_picture_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Picture $picture
     * @return Response
     */
    public function edit(Request $request, Picture $picture): Response
    {
        $form = $this->createForm(PictureType::class, $picture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_picture_index');
        }

        return $this->render('Admin/picture/edit.html.twig', [
            'picture' => $picture,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_picture_delete", methods={"DELETE"})
     * @param Request $request
     * @param Picture $picture
     * @return Response
     */
    public function delete(Request $request, Picture $picture): Response
    {
        if ($this->isCsrfTokenValid('delete'.$picture->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($picture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_picture_index');
    }
}
