<?php

namespace App\Controller\client;

use App\Repository\LivresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    // Use array_sum on $panier values after each update.

    #[Route('/increment/{id}', name: 'app_client_panier_increment')]
    public function increment($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (isset($panier[$id])) {
            $panier[$id]++;
        }

        $session->set('panier', $panier);
        $session->set('panierCount', array_sum($panier));

        return $this->redirectToRoute('app_client_panier_index');
    }

    #[Route('/decrement/{id}', name: 'app_client_panier_decrement')]
    public function decrement($id, SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (isset($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }

        $session->set('panier', $panier);
        $session->set('panierCount', array_sum($panier));

        return $this->redirectToRoute('app_client_panier_index');
    }

    #[Route('/add', name: 'app_client_panier_add')]
    public function addToCart(Request $request, SessionInterface $session)
    {
        $productId = $request->get('id');
        $quantity = (int) $request->get('qte', 1);
        $panier = $session->get('panier', []);

        if (isset($panier[$productId])) {
            $panier[$productId] += $quantity;
        } else {
            $panier[$productId] = $quantity;
        }

        $session->set('panier', $panier);
        $session->set('panierCount', array_sum($panier));

        return $this->redirectToRoute('app_client_panier_index');
    }

    #[Route('/remove/{id}', name: 'app_client_panier_remove')]
    public function removeFromCart(Request $request, SessionInterface $session)
    {
        $panier = $session->get('panier', []);
        $productId = $request->get('id');

        if (isset($panier[$productId])) {
            unset($panier[$productId]);
        }

        $session->set('panier', $panier);
        $session->set('panierCount', array_sum($panier));

        return $this->redirectToRoute('app_client_panier_index');
    }

    #[Route('/', name: 'app_client_panier_index')]
    public function show(SessionInterface $session, LivresRepository $livreRepository)
    {
        $panier = $session->get('panier', []);
        $livresDansPanier = [];
        foreach ($panier as $livreId => $qte) {
            $qte = (int) $qte ?: 1;
            $livre = $livreRepository->find($livreId);
            if ($livre) {
                $livresDansPanier[] = [
                    'livre' => $livre,
                    'qte'   => $qte,
                ];
            }
        }

        $session->set('panier', $panier);
        $session->set('panierCount', array_sum($panier));

        return $this->render('client/panier/index.html.twig', [
            'items' => $livresDansPanier,
        ]);
    }
    #[Route('/commander', name: 'app_client_panier_commander')]
    public function commander(SessionInterface $session)
    {
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('info', 'Votre panier est vide.');
            return $this->redirectToRoute('app_client_panier_index');
        }

        return $this->redirectToRoute('app_client_commande_index');
    }
}