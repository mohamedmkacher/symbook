<?php
namespace App\Repository;

use App\Entity\Commande;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }
    public function getChiffreAffairesTotal(): float
    {
        return $this->createQueryBuilder('c')
            ->select('SUM(c.total) as chiffreAffaires')
            ->getQuery()
            ->getSingleScalarResult() ?? 0;
    }
    // src/Repository/CommandeRepository.php
    public function findByUserOrderedByDate($user)
    {
        return $this->createQueryBuilder('c')
            ->join('c.panier', 'p')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findAllOrderedByDate()
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findByUser(User $user)
    {
        return $this->createQueryBuilder('c')
            ->join('c.panier', 'p')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
}