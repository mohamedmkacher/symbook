<?php

namespace App\Repository;

use App\Entity\EtatPanier;
use App\Entity\panier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<panier>
 */
class PanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, panier::class);
    }

    public function findActiveCart($user): ?panier
    {
        return $this->createQueryBuilder('p')
            ->where('p.user = :user')
            ->andWhere('p.etat_panier = :state')
            ->setParameter('user', $user)
            ->setParameter('state', EtatPanier::EN_COURS)
            ->getQuery()
            ->getOneOrNullResult();
    }

//    /**
//     * @return panier[] Returns an array of panier objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?panier
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
