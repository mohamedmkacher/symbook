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
            ->select('l as livre, COUNT(lp.id) as ventes')
            ->leftJoin('App\Entity\LignePanier', 'lp', 'WITH', 'lp.livre = l.id')
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

    public function findFilteredBooks(?int $categoryId = null, ?string $search = null): array
    {
        $qb = $this->createQueryBuilder('l')
            ->leftJoin('l.categorie', 'c')
            ->orderBy('l.titre', 'ASC');

        if ($categoryId) {
            $qb->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        if ($search) {
            $qb->andWhere('l.titre LIKE :search OR l.resume LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
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
}
