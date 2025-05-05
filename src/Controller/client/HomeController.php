<?php
// src/Controller/Client/HomeController.php
namespace App\Controller\client;

use App\Repository\CategoriesRepository;
use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('')]
class HomeController extends AbstractController
{
    #[Route('/', name: 'app_client_home')]
    public function index(
        LivresRepository $livresRepository,
        CategoriesRepository $categoriesRepository
    ): Response {
        // livres récemment ajoutés
        $nouveautes = $livresRepository->findBy(
            [],
            ['id' => 'DESC'],
            4
        );

        // livres les plus vendus
        $bestSellers = $livresRepository->findMostPopularBooks(4);

        // Toutes les catégories
        $categories = $categoriesRepository->findAll();

        return $this->render('client/home.html.twig', [
            'nouveautes' => $nouveautes,
            'bestSellers' => $bestSellers,
            'categories' => $categories,
        ]);
    }
}