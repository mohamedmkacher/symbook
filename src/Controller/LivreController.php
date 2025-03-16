<?php

namespace App\Controller;

use App\Entity\Livres;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/livre/create', name: 'app_livre_create')]
    public function create(EntityManagerInterface $entityManager): Response
    {
        $livre = new Livres();
        $livre->setImage("https://picsum.photos/200/?id=4")
            ->setIsbn("456-852-741-963")
            ->setTitre("Titre 1")
            ->setSlug("titre-1")
            ->setEditeur("Editeur 1")
            ->setResume("Resume 1")
            ->setPrix(35.5)
            ->setDateEdition(new \DateTime("2023-02-02"));
        $entityManager->persist($livre);
        $entityManager->flush();
        return $this->redirectToRoute('app_livre');

    }

    #[Route('/livre/show/{id}', name: 'app_livre_show')]
    public function show(LivresRepository $livresRepository, $id): Response
    {
        $livre = $livresRepository->find($id);
        if (null === $livre) {
            //throw $this->createNotFoundException("Le livre n'existe pas.");
            throw new NotFoundHttpException("Le livre n'existe pas.");
        }else{
            return $this->render('livre/show.html.twig', ['livre' => $livre]);
        }
    }


    #[Route('/livre/show2', name: 'app_livre_show2')]
    public function show2(LivresRepository $livresRepository): Response
    {
        $livre = $livresRepository->findOneBy(['titre' => "Titre 1", 'slug' => "titre-1"]);
        if (null === $livre) {
            //throw $this->createNotFoundException("Le livre n'existe pas.");
            throw new NotFoundHttpException("Le livre n'existe pas.");
        }
        dd($livre);
    }

    #[Route('/livre/show3', name: 'app_livre_show3')]
    public function show3(LivresRepository $livresRepository): Response
    {
        $livres = $livresRepository->findBy(['titre' => "Titre 1"], ['id' => 'DESC']);
        if (null === $livres) {
            //throw $this->createNotFoundException("Le livre n'existe pas.");
            throw new NotFoundHttpException("Le livre n'existe pas.");
        }
        dd($livres);
    }

    #[Route('/livre/show4/{id}', name: 'app_livre_show4')]
    public function show4(Livres $livre): Response
    {

        if (null === $livre) {
            //throw $this->createNotFoundException("Le livre n'existe pas.");
            throw new NotFoundHttpException("L'livre n'existe pas.");
        }
        dd($livre);
    }

    #[Route('/livre/all', name: 'app_livre_all')]
    public function all(LivresRepository $livresRepository): Response
    {
        $livres = $livresRepository->findAll();
        return $this->render('livre/all.html.twig', ['livres' => $livres]);
    }

    #[Route('/livre/delete/{id}', name: 'app_livre_delete')]
    public function delete(LivresRepository $livresRepository, EntityManagerInterface $entityManager, $id)
    {
        $livre = $livresRepository->find($id);
        $entityManager->remove($livre);
        $entityManager->flush();
        return $this->redirectToRoute('app_livre_all');
    }

    /*
        #[Route('/livre/delete/{id}', name: 'app_livre_delete')]
        public function delete( EntityManagerInterface $entityManager,Livres $livre): Response
        {

            $entityManager->remove($livre);
            $entityManager->flush();

        }
    */

    #[Route('/livre/update/{id}', name: 'app_livre_update')]
    public function update(LivresRepository $livresRepository, EntityManagerInterface $entityManager, $id)
    {
        $livre = $livresRepository->find($id);

        $livre->setPrix($livre->getPrix() * 1.1);
        $entityManager->persist($livre);
        $entityManager->flush();

        return $this->redirectToRoute('app_livre_all');


    }
}
