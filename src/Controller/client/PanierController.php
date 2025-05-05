<?php
// src/Controller/Client/PanierController.php
namespace App\Controller\client;

use App\Entity\Commande;
use App\Entity\EtatPanier;
use App\Entity\Livres;
use App\Entity\panier;
use App\Entity\LignePanier;
use App\Repository\PanierRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
    #[Route('/', name: 'app_client_panier_index', methods: ['GET'])]
    public function index(PanierRepository $panierRepository): Response
    {
        $panier = $panierRepository->findActiveCart($this->getUser());

        return $this->render('client/panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/add/{id}', name: 'app_client_panier_add', methods: ['POST'])]
    public function add(
        Livres                 $livre,
        Request                $request,
        EntityManagerInterface $entityManager,
        PanierRepository       $panierRepository
    ): Response {
        $quantity = $request->request->getInt('quantity', 1);

        if ($quantity <= 0) {
            $this->addFlash('error', 'La quantité doit être supérieure à zéro');
            return $this->redirectToRoute('app_client_livre_show', ['id' => $livre->getId()]);
        }

        $user = $this->getUser();
        $panier = $panierRepository->findActiveCart($user);

        if (!$panier) {
            $panier = new panier();
            $panier->setUser($user);
            $panier->setEtatPanier(EtatPanier::EN_COURS);
            $entityManager->persist($panier);
        }

        $existingItem = null;
        foreach ($panier->getLignesPanier() as $ligne) {
            if ($ligne->getLivre()->getId() === $livre->getId()) {
                $existingItem = $ligne;
                break;
            }
        }

        if ($existingItem) {
            $existingItem->setQte($existingItem->getQte() + $quantity);
        } else {
            $lignePanier = new LignePanier();
            $lignePanier->setLivre($livre);
            $lignePanier->setQte($quantity);
            $lignePanier->setPanier($panier);
            $entityManager->persist($lignePanier);
        }

        $entityManager->flush();
        $this->updateCartCount($panier);

        $this->addFlash('success', 'Le livre a été ajouté à votre panier');
        return $this->redirectToRoute('app_client_panier_index');
    }
    private function updateCartCount(?panier $panier): void
    {
        $count = 0;
        if ($panier) {
            foreach ($panier->getLignesPanier() as $ligne) {
                $count += $ligne->getQte();
            }
        }
        $this->requestStack->getSession()->set('panierCount', $count);
    }

    #[Route('/update/{id}', name: 'app_client_panier_update', methods: ['POST'])]
    public function update(
        LignePanier $lignePanier,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $action = $request->request->get('action');

        if ($action === 'increase') {
            $lignePanier->setQte($lignePanier->getQte() + 1);
        } elseif ($action === 'decrease' && $lignePanier->getQte() > 1) {
            $lignePanier->setQte($lignePanier->getQte() - 1);
        }

        $entityManager->flush();
        $this->updateCartCount($lignePanier->getPanier());

        return $this->redirectToRoute('app_client_panier_index');
    }

    #[Route('/remove/{id}', name: 'app_client_panier_remove', methods: ['POST'])]
    public function remove(
        LignePanier $lignePanier,
        EntityManagerInterface $entityManager
    ): Response {
        $panier = $lignePanier->getPanier();
        $entityManager->remove($lignePanier);
        $entityManager->flush();
        $this->updateCartCount($panier);

        $this->addFlash('success', 'L\'article a été supprimé du panier');
        return $this->redirectToRoute('app_client_panier_index');
    }

    #[Route('/checkout', name: 'app_client_panier_checkout', methods: ['GET', 'POST'])]
    public function checkout(
        PanierRepository $panierRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $panier = $panierRepository->findActiveCart($this->getUser());

        if (!$panier || $panier->getLignesPanier()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide');
            return $this->redirectToRoute('app_client_panier_index');
        }

        // Création de la commande
        $commande = new Commande();
        $commande->setTotal($this->calculateTotal($panier));
        $commande->setDate(new DateTime());
        $commande->setPanier($panier);

        // Mise à jour du panier
        $panier->setEtatPanier(EtatPanier::VALIDE);

        $entityManager->persist($commande);
        $entityManager->flush();
        $this->updateCartCount($panier);

        $this->addFlash('success', 'Votre commande a été passée avec succès');
        return $this->redirectToRoute('app_client_commande_show', ['id' => $commande->getId()]);
    }

    private function calculateTotal(panier $panier): float
    {
        $total = 0;
        foreach ($panier->getLignesPanier() as $ligne) {
            $total += $ligne->getLivre()->getPrix() * $ligne->getQte();
        }
        return $total;
    }


}