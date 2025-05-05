<?php

declare(strict_types=1);

namespace App\Controller\client;

use App\Repository\CategoriesRepository;
use App\Repository\LivresRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


// src/Controller/Client/CategorieController.php
#[Route('/categories')]
class CategorieController extends AbstractController
{
    #[Route('/', name: 'app_client_categorie_index', methods: ['GET'])]
    public function index(CategoriesRepository $categoriesRepository): Response
    {
        return $this->render('client/categorie/index.html.twig', [
            'categories' => $categoriesRepository->findAllWithBookCount(),
        ]);
    }





    #[Route('/{slug}', name: 'app_client_categorie_show', methods: ['GET'])]
    public function show(
        string $slug,
        CategoriesRepository $categoriesRepository,
        LivresRepository $livresRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $categorie = $categoriesRepository->findOneBySlug($slug);

        if (!$categorie) {
            throw $this->createNotFoundException('Catégorie non trouvée');
        }

        $page = $request->query->getInt('page', 1);
        $livres = $livresRepository->findPaginatedByCategory(
            $categorie->getId(),
            $paginator,    // The PaginatorInterface service
            $page,         // Current page number from request
            6              // Items per page
        );

        return $this->render('client/categorie/show.html.twig', [
            'categorie' => $categorie,
            'livres' => $livres,
        ]);
    }
}