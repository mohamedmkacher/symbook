<?php

declare(strict_types=1);

namespace App\Controller\client;


use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commandes')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_client_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): void
    {
        return ;
    }

    #[Route('/{id}', name: 'app_client_commande_show', methods: ['GET'])]
    public function show(Commande $commande): void
    {

       return ;
    }
}