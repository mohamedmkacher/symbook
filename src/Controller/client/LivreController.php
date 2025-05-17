<?php

namespace App\Controller\client;


use App\Entity\Livres;
use App\Repository\LivresRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/livres')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'app_client_livre_index', methods: ['GET'])]
    public function index(
        LivresRepository $livresRepository,
        CategoriesRepository $categoriesRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        $categoryId = $request->query->get('category');
        $search = $request->query->get('search');

        // Crée le QueryBuilder de base
        $queryBuilder = $livresRepository->createQueryBuilder('l');

        // Applique les filtres
        if ($categoryId) {
            $queryBuilder->andWhere('l.categorie = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }
        if ($search) {
            $queryBuilder->andWhere('l.titre LIKE :search OR l.resume LIKE :search')
                ->setParameter('search', '%'.$search.'%');
        }

        // Pagine les résultats
        $livres = $paginator->paginate(
            $queryBuilder->getQuery(), // La requête Doctrine
            $request->query->getInt('page', 1), // Numéro de page
            8 // Nombre d'items par page
        );

        return $this->render('client/livre/index.html.twig', [
            'livres' => $livres, // Maintenant un objet SlidingPagination
            'categories' => $categoriesRepository->findAll(),
            'selectedCategory' => $categoryId,
            'searchQuery' => $search,
        ]);
    }

    #[Route('/{id}', name: 'app_client_livre_show', methods: ['GET'])]
    public function show(Livres $livre): Response
    {
        return $this->render('client/livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

   #[Route('/{id}/add-to-cart', name: 'app_client_livre_add_to_cart', methods: ['POST'])]
    public function addToCart(
        Livres                 $livre,
        Request                $request,
        EntityManagerInterface $entityManager
    ): void
   {
        return ;
    }
}