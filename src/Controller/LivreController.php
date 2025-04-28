<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivreType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class LivreController extends AbstractController
{
    private $exit;


    #[Route('admin/livre/add', name: 'app_admin_livre_add')]
    public function add(EntityManagerInterface $entityManager, Request $request): Response
    {
        $livre = new Livres();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush();
            $this->addFlash('success', 'Ajout du livre avec succès');
            return $this->redirectToRoute('app_admin_livre_all');
        }
        return $this->render('livre/add.html.twig', ['form' => $form]);

    }

    #[Route('admin/livre/show/{id}', name: 'app_admin_livre_show')]
    public function show(LivresRepository $livresRepository, $id): Response
    {
        $livre = $livresRepository->find($id);
        if (null === $livre) {
            //throw $this->createNotFoundException("Le livre n'existe pas.");
            throw new NotFoundHttpException("Le livre n'existe pas.");
        } else {
            return $this->render('livre/show.html.twig', ['livre' => $livre]);
        }
    }

    #[Route('admin/livre/all', name: 'app_admin_livre_all')]
    public function all(LivresRepository $livresRepository, PaginatorInterface $paginator, Request $request): Response
    {


        $livres = $paginator->paginate(
            $livresRepository->findAll(), /* query NOT result */
            $request->query->getInt('page', 1), /* page number */
            10 /* limit per page */
        );
        return $this->render('livre/all.html.twig', ['livres' => $livres]);
    }

    #[Route('admin/livre/delete/{id}', name: 'app_admin_livre_delete')]
    public function delete(LivresRepository $livresRepository, EntityManagerInterface $entityManager, $id): RedirectResponse
    {
        $livre = $livresRepository->find($id);
        $entityManager->remove($livre);
        $entityManager->flush();
        $this->addFlash('success', 'Supression du livre avec succès');


        return $this->redirectToRoute('app_admin_livre_all');
    }

    #[Route('admin/livre/categorie/{id}', name: 'app_admin_livre_all_categorie')]
    public function allByCat(LivresRepository $livresRepository, PaginatorInterface $paginator, Request $request, $id): Response
    {
        $livres = $paginator->paginate(
            $livresRepository->findBy(["categorie" => $id]),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('livre/all.html.twig', ['livres' => $livres]);
    }

    #[Route('admin/livre/update/{id}', name: 'app_admin_livre_update')]
    public function update(LivresRepository $livresRepository, EntityManagerInterface $entityManager, Livres $livre, Request $request): RedirectResponse|Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();
            $this->addFlash('success', 'Mise à jour du livre avec succès');
            return $this->redirectToRoute('app_admin_livre_all');
        }
        return $this->render('livre/update.html.twig', ['form' => $form]);


    }
}
