<?php

namespace App\Controller\client;

use App\Entity\Commande;
use App\Entity\EtatPanier;
use App\Entity\LignePanier;
use App\Entity\Paiement;
use App\Entity\panier;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_client_commande_index', methods: ['GET'])]
    public function index(SessionInterface $session, LivresRepository $livreRepository): Response
    {
        // Récupérer le panier de la session
        $panierSession = $session->get('panier', []);

        // Si le panier est vide, rediriger vers la page du panier
        if (empty($panierSession)) {
            $this->addFlash('info', 'Votre panier est vide.');
            return $this->redirectToRoute('app_client_panier_index');
        }

        // Récupérer les livres du panier
        $livresDansPanier = [];
        $total = 0;

        foreach ($panierSession as $livreId => $qte) {
            $qte = (int) $qte ?: 1;
            $livre = $livreRepository->find($livreId);
            if ($livre) {
                $livresDansPanier[] = [
                    'livre' => $livre,
                    'qte'   => $qte,
                ];
                $total += $livre->getPrix() * $qte;
            }
        }

        return $this->render('client/commande/index.html.twig', [
            'items' => $livresDansPanier,
            'total' => $total,
        ]);
    }

    #[Route('/paiement', name: 'app_client_commande_paiement', methods: ['GET', 'POST'])]
    public function paiement(Request $request, SessionInterface $session, LivresRepository $livreRepository): Response
    {
        // Récupérer le panier de la session
        $panierSession = $session->get('panier', []);

        // Si le panier est vide, rediriger vers la page du panier
        if (empty($panierSession)) {
            $this->addFlash('info', 'Votre panier est vide.');
            return $this->redirectToRoute('app_client_panier_index');
        }

        // Récupérer les livres du panier
        $livresDansPanier = [];
        $total = 0;

        foreach ($panierSession as $livreId => $qte) {
            $qte = (int) $qte ?: 1;
            $livre = $livreRepository->find($livreId);
            if ($livre) {
                $livresDansPanier[] = [
                    'livre' => $livre,
                    'qte'   => $qte,
                ];
                $total += $livre->getPrix() * $qte;
            }
        }

        // Traiter le formulaire de paiement si c'est une requête POST
        if ($request->isMethod('POST')) {
            $modePaiement = $request->request->get('mode_paiement');

            // Stocker le mode de paiement dans la session pour l'utiliser lors de la confirmation
            $session->set('mode_paiement', $modePaiement);

            // Rediriger vers la page de confirmation
            return $this->redirectToRoute('app_client_commande_confirmation');
        }

        return $this->render('client/commande/paiement.html.twig', [
            'items' => $livresDansPanier,
            'total' => $total,
        ]);
    }

    #[Route('/confirmation', name: 'app_client_commande_confirmation', methods: ['GET', 'POST'])]
    public function confirmation(
        Request $request,
        SessionInterface $session,
        LivresRepository $livreRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // Récupérer le panier et le mode de paiement de la session
        $panierSession = $session->get('panier', []);
        $modePaiement = $session->get('mode_paiement');

        // Si le panier est vide ou le mode de paiement n'est pas défini, rediriger
        if (empty($panierSession) || !$modePaiement) {
            $this->addFlash('info', 'Une erreur est survenue. Veuillez réessayer.');
            return $this->redirectToRoute('app_client_panier_index');
        }

        // Récupérer les livres du panier
        $livresDansPanier = [];
        $total = 0;

        foreach ($panierSession as $livreId => $qte) {
            $qte = (int) $qte ?: 1;
            $livre = $livreRepository->find($livreId);
            if ($livre) {
                $livresDansPanier[] = [
                    'livre' => $livre,
                    'qte'   => $qte,
                ];
                $total += $livre->getPrix() * $qte;
            }
        }

        // Si c'est une requête POST, enregistrer la commande
        if ($request->isMethod('POST')) {
            // Créer une nouvelle entité Panier
            $panierEntity = new panier();
            $panierEntity->setEtatPanier(EtatPanier::VALIDE);

            // Associer l'utilisateur connecté au panier si disponible
            if ($this->getUser()) {
                $panierEntity->setUser($this->getUser());
            }

            // Ajouter les lignes de panier
            foreach ($livresDansPanier as $item) {
                $lignePanier = new LignePanier();
                $lignePanier->setLivre($item['livre']);
                $lignePanier->setQte($item['qte']);
                $lignePanier->setPanier($panierEntity);

                $entityManager->persist($lignePanier);
                $panierEntity->addLignesPanier($lignePanier);
            }

            $entityManager->persist($panierEntity);

            // Créer une nouvelle commande
            $commande = new Commande();
            $commande->setDate(new \DateTime());
            $commande->setTotal($total);
            $commande->setPanier($panierEntity);

            $entityManager->persist($commande);

            // AJOUT: Créer un nouvel objet Paiement
            $paiement = new Paiement();
            $paiement->setCommande($commande);
            $paiement->setModePaiement($modePaiement);
            $paiement->setMontant($total);
            $paiement->setDatePaiement(new \DateTime());
            $paiement->setReference('REF-' . uniqid());
            $paiement->setStatut('validé');

            $entityManager->persist($paiement);

            // IMPORTANT: Associer le paiement à la commande
            $commande->setPaiement($paiement);

            $entityManager->flush();

            // Vider le panier de la session
            $session->remove('panier');
            $session->remove('panierCount');
            $session->remove('mode_paiement');

            // Rediriger vers la page de succès
            return $this->redirectToRoute('app_client_commande_succes', [
                'id' => $commande->getId()
            ]);
        }

        // Générer un numéro de commande temporaire pour l'affichage
        $numeroCommande = 'CMD-' . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);

        return $this->render('client/commande/confirmation.html.twig', [
            'items' => $livresDansPanier,
            'total' => $total,
            'mode_paiement' => $modePaiement,
            'numero_commande' => $numeroCommande
        ]);
    }

    #[Route('/succes/{id}', name: 'app_client_commande_succes', methods: ['GET'])]
    public function succes(Commande $commande): Response
    {
        return $this->render('client/commande/succes.html.twig', [
            'commande' => $commande
        ]);
    }
}