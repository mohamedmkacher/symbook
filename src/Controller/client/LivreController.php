<?php
// src/Controller/Client/LivreController.php
namespace App\Controller\client;

use App\Entity\LignePanier;
use App\Entity\panier;
use App\Entity\Livres;
use App\Repository\LivresRepository;
use App\Repository\CategoriesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/livres')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'app_client_livre_index', methods: ['GET'])]
    public function index(
        LivresRepository $livresRepository,
        CategoriesRepository $categoriesRepository,
        Request $request
    ): Response {

        $categoryId = $request->query->get('category');
        $search = $request->query->get('search');


        $livres = $livresRepository->findFilteredBooks($categoryId, $search);
        $categories = $categoriesRepository->findAll();

        return $this->render('client/livre/index.html.twig', [
            'livres' => $livres,
            'categories' => $categories,
            'selectedCategory' => $categoryId,
            'searchQuery' => $search,
        ]);
    }

    #[Route('/{id}', name: 'app_client_livre_show', methods: ['GET'])]
    public function show(Livres $livre): Response
    {
        return $this->render('client/livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/add-to-cart', name: 'app_client_livre_add_to_cart', methods: ['POST'])]
    public function addToCart(
        Livres                 $livre,
        Request                $request,
        EntityManagerInterface $entityManager
    ): Response {
        $quantity = $request->request->getInt('quantity', 1);

        // Vérification de la quantité
        if ($quantity <= 0) {
            $this->addFlash('error', 'La quantité doit être supérieure à zéro');
            return $this->redirectToRoute('app_client_livre_show', ['id' => $livre->getId()]);
        }

        // Récupération du panier actif de l'utilisateur
        $user = $this->getUser();
        $panier = $entityManager->getRepository(panier::class)->findActiveCart($user);

        if (!$panier) {
            $panier = new panier();
            $panier->setUser($user);
            $entityManager->persist($panier);
        }

        // Vérification si le livre est déjà dans le panier
        $existingItem = null;
        foreach ($panier->getLignesPanier() as $ligne) {
            if ($ligne->getLivre()->getId() === $livre->getId()) {
                $existingItem = $ligne;
                break;
            }
        }

        if ($existingItem) {
            // Mise à jour de la quantité
            $existingItem->setQte($existingItem->getQte() + $quantity);
        } else {
            // Création d'une nouvelle ligne de panier
            $lignePanier = new LignePanier();
            $lignePanier->setLivre($livre);
            $lignePanier->setQte($quantity);
            $lignePanier->setPanier($panier);
            $entityManager->persist($lignePanier);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Le livre a été ajouté à votre panier');
        return $this->redirectToRoute('app_client_livre_show', ['id' => $livre->getId()]);
    }
}