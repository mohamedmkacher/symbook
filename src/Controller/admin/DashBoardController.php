<?php

namespace App\Controller\admin;

use App\Repository\CommandeRepository;
use App\Repository\LivresRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class DashBoardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(
        CommandeRepository $commandeRepository,
        LivresRepository $livresRepository,
        UserRepository $userRepository
    ): Response {

        $stats = [
            'total_commandes' => $commandeRepository->count([]),
            'total_livres' => $livresRepository->count([]),
            'total_clients' => $userRepository->count([]),
            'chiffre_affaires' => $commandeRepository->getChiffreAffairesTotal(),
        ];


        $dernieresCommandes = $commandeRepository->findBy(
            [],
            ['date' => 'DESC'],
            5
        );


        $livresPopulaires = $livresRepository->findMostPopularBooks(3);

        return $this->render('admin/dashboard/index.html.twig', [
            'stats' => $stats,
            'dernieres_commandes' => $dernieresCommandes,
            'livres_populaires' => $livresPopulaires,
        ]);
    }
}