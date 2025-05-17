<?php

namespace App\Controller\client;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CommandeClientController extends AbstractController
{
    private $commandeRepository;

    public function __construct(CommandeRepository $commandeRepository)
    {
        $this->commandeRepository = $commandeRepository;
    }

    #[Route('/compte/commandes', name: 'app_client_commandes')]
    public function index(): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les commandes de l'utilisateur
        $commandes = $this->commandeRepository->findByUser($user);

        return $this->render('client/commande/liste.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/compte/commandes/{id}', name: 'app_client_commande_details')]
    public function details(int $id): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Récupérer la commande spécifique
        $commande = $this->commandeRepository->find($id);

        // Vérifier que la commande existe et appartient à l'utilisateur
        if (!$commande || $commande->getPanier()->getUser() !== $user) {
            throw $this->createNotFoundException('Commande non trouvée');
        }

        return $this->render('client/commande/details.html.twig', [
            'commande' => $commande,
        ]);
    }
}