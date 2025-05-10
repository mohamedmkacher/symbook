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
        Request $request, PaginatorInterface $paginator,

    ): Response {


        $categoryId = $request->query->get('category');
        $search = $request->query->get('search');

        $page = $request->query->getInt('page', 1);
        $livres = $livresRepository->findFilteredBooks($categoryId, $search,$paginator,$page,8);
        $categories = $categoriesRepository->findAll();

        return $this->render('client/livre/index.html.twig', [
            'livres' => $livres,
            'categories' => $categories,
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