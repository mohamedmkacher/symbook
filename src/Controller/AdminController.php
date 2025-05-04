<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function dashboard(
        LivresRepository     $livreRepository,
        CategoriesRepository $categorieRepository
    ): Response
    {
        return $this->render('admin/dashboard1.html.twig', [
            'book_count' => $livreRepository->count([]),
            'category_count' => $categorieRepository->count([]),
            'monthly_revenue' => 1254.50,
            'recent_books' => $livreRepository->findBy([], ['dateEdition' => 'DESC'], 5),
            'recent_activity' => []


        ]);
    }
}