<?php

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

        $nouveautes = $livresRepository->findBy(
            [],
            ['id' => 'DESC'],
            8
        );


        $bestSellers = $livresRepository->findMostPopularBooks(8);


        $categories = $categoriesRepository->findAll();

        return $this->render('client/home.html.twig', [
            'nouveautes' => $nouveautes,
            'bestSellers' => $bestSellers,
            'categories' => $categories,
        ]);
    }
}