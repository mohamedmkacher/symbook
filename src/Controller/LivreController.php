<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Form\LivreType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class LivreController extends AbstractController
{
    private $exit;

    #[Route('/livre', name: 'app_livre')]
    public function index(): Response
    {
        return $this->render('livre/index.html.twig', [
            'controller_name' => 'LivreController',
        ]);
    }

    #[Route('admin/livre/add', name: 'app_admin_livre_add')]
    public function add(EntityManagerInterface $entityManager ,Request $request): Response
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


    #[Route('admin/livre/show2', name: 'app_admin_livre_show2')]
    public function show2(LivresRepository $livresRepository): Response
    {
        $livre = $livresRepository->findOneBy(['titre' => "Titre 1", 'slug' => "titre-1"]);
        if (null === $livre) {
            //throw $this->createNotFoundException("Le livre n'existe pas.");
            throw new NotFoundHttpException("Le livre n'existe pas.");
        }
        dd($livre);
    }

    #[Route('admin/livre/show3', name: 'app_admin_livre_show3')]
    public function show3(LivresRepository $livresRepository): Response
    {
        $livres = $livresRepository->findBy(['titre' => "Titre 1"], ['id' => 'DESC']);
        if (null === $livres) {
            //throw $this->createNotFoundException("Le livre n'existe pas.");
            throw new NotFoundHttpException("Le livre n'existe pas.");
        }
        dd($livres);
    }

    #[Route('admin/livre/show4/{id}', name: 'app_admin_livre_show4')]
    public function show4(Livres $livre): Response
    {

        if (null === $livre) {
            //throw $this->createNotFoundException("Le livre n'existe pas.");
            throw new NotFoundHttpException("Le livre n'existe pas.");
        }
        dd($livre);
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
    public function delete(LivresRepository $livresRepository, EntityManagerInterface $entityManager, $id)
    {
        $livre = $livresRepository->find($id);
        $entityManager->remove($livre);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_livre_all');
    }

    /*
        #[Route('/livre/delete/{id}', name: 'app_livre_delete')]
        public function delete( EntityManagerInterface $entityManager,livres $livre): Response
        {

            $entityManager->remove($livre);
            $entityManager->flush();

        }
    */

    #[Route('admin/livre/update/{id}', name: 'app_admin_livre_update')]
    public function update(LivresRepository $livresRepository, EntityManagerInterface $entityManager, $id)
    {
        $livre = $livresRepository->find($id);

        $livre->setPrix($livre->getPrix() * 1.1);
        $entityManager->persist($livre);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_livre_all');


    }
}
