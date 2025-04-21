<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategorieType;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CategorieController extends AbstractController
{
    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }

    #[Route('/admin/categorie/all', name: 'app_admin_categorie_all')]
    public function all(CategoriesRepository $categoriesRepository): Response
    {
        $categories = $categoriesRepository->findAll();
        return $this->render('categorie/all.html.twig', [
            'categories' => $categories,

        ]);

    }

    #[Route('/admin/categorie/add', name: 'app_admin_categorie_add')]
    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categories();
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($categorie);
            $entityManager->flush();
            $this->addFlash('success', 'Ajout de la catégorie avec succès');
            return $this->redirectToRoute('app_admin_categorie_all');
        }
        return $this->render('categorie/add.html.twig', ['form' => $form]);


    }

    #[Route('/admin/categorie/update/{id}', name: 'app_admin_categorie_update')]
    public function update(Request $request, EntityManagerInterface $entityManager, Categories $categorie): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();
            $this->addFlash('success', 'Mise à jour de la catégorie avec succès');
            return $this->redirectToRoute('app_admin_categorie_all');
        }
        return $this->render('categorie/update.html.twig', ['form' => $form]);


    }

}
