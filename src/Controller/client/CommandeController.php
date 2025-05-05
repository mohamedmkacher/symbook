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
    public function index(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();

        return $this->render('client/commande/index.html.twig', [
            'commandes' => $commandeRepository->findByUserOrderedByDate($user),
        ]);
    }

    #[Route('/{id}', name: 'app_client_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        // Vérification que la commande appartient bien à l'utilisateur connecté
        if ($commande->getPanier()->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas accéder à cette commande');
        }

        return $this->render('client/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }
}