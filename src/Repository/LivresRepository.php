<?php

namespace App\Repository;

use App\Entity\Livres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Livres>
 */
class LivresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Livres::class);
    }

    public function findMostPopularBooks(int $limit = 5): array
    {
        return $this->createQueryBuilder('l')
            ->select('l AS livre, COUNT(lp.id) AS ventes')
            // Jointure pour récupérer les lignes panier associées au livre
            ->leftJoin('App\Entity\LignePanier', 'lp', 'WITH', 'lp.livre = l.id')
            // Jointure pour récupérer le panier afin de vérifier son état
            ->leftJoin('lp.panier', 'p')
            ->where('p.etat_panier = :validState')
            ->setParameter('validState', 'VALIDE') // Adaptez la valeur selon votre logique
            ->groupBy('l.id')
            ->orderBy('ventes', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }


    public function findPaginatedByCategory(
        int $categoryId,
        PaginatorInterface $paginator,  // Renamed from $page to $paginator for clarity
        int $currentPage,              // Added current page parameter
        int $limit
    ) {
        $query = $this->createQueryBuilder('l')
            ->where('l.categorie = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('l.titre', 'ASC')
            ->getQuery();

        return $paginator->paginate(
            $query,      // Query to paginate
            $currentPage, // Current page number
            $limit       // Items per page
        );
    }

    public function findFilteredBooks(
        $categoryId,
        ?string $search,
        PaginatorInterface $paginator,
        int $currentPage,
        int $limit
    ) {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.categorie', 'c')
            ->orderBy('l.titre', 'ASC');

        if ($categoryId!='') {
            $qb->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        if ($search) {
            $qb->andWhere('l.titre LIKE :search OR l.resume LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $paginator->paginate(
            $qb->getQuery(),
            $currentPage,
            $limit
        );
    }


    //    /**
    //     * @return livres[] Returns an array of livres objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('l.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?livres
    //    {
    //        return $this->createQueryBuilder('l')
    //            ->andWhere('l.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
    public function findPaginatedByCategoryAndSearch(
        ?int $getId,
        float|bool|int|string|null $searchTerm,
        PaginatorInterface $paginator,
        int $page,
        int $limit
    ) {
        $queryBuilder = $this->createQueryBuilder('l')
            ->where('l.categorie = :catId')
            ->setParameter('catId', $getId);

        // Si un terme de recherche est fourni, on ajoute une condition
        if (!empty($searchTerm)) {
            $queryBuilder
                ->andWhere('l.titre LIKE :searchTerm OR l.editeur LIKE :searchTerm OR l.resume LIKE :searchTerm')
                ->setParameter('searchTerm', '%'.$searchTerm.'%');
        }

        $query = $queryBuilder->getQuery();

        return $paginator->paginate(
            $query,
            $page,
            $limit
        );
    }
}
