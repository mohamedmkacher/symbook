<?php

namespace App\Controller\admin;


use App\Entity\Commande;
use App\Entity\EtatPanier;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/commandes')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_admin_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('admin/commande/index.html.twig', [
            'commandes' => $commandeRepository->findAllOrderedByDate(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_commande_show', methods: ['GET'])]
    public function show(Commande $commande): Response
    {
        return $this->render('admin/commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): void
    {
       return ;
    }

    #[Route('/{id}/mark-as-shipped', name: 'app_admin_commande_mark_shipped', methods: ['POST'])]
    public function markAsShipped(Commande $commande, EntityManagerInterface $entityManager): void
    {
       return ;
    }
}