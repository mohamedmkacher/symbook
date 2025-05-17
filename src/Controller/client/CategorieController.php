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

        // Récupération du terme de recherche depuis l'URL (GET)
        $searchTerm = $request->query->get('search', '');

        // Récupération de la page courante
        $page = $request->query->getInt('page', 1);

        // Méthode à implémenter qui intègre la logique de recherche
        $livres = $livresRepository->findPaginatedByCategoryAndSearch(
            $categorie->getId(),
            $searchTerm,
            $paginator,
            $page,
            8
        );

        return $this->render('client/categorie/show.html.twig', [
            'categorie' => $categorie,
            'livres' => $livres,
            'searchTerm' => $searchTerm, // Pour ré-afficher éventuellement la valeur dans la barre de recherche
        ]);
    }
}