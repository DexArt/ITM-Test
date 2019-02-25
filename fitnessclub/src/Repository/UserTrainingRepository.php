<?php

namespace App\Repository;

use App\Entity\UserTraining;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method UserTraining|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserTraining|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserTraining[]    findAll()
 * @method UserTraining[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTrainingRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, UserTraining::class);
    }


    public function findActiveUserTrainings($group_training_id)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.is_deleted = :val','u.groupTraining = :group_training_id')
            ->setParameter('val', false)
            ->setParameter('group_training_id',$group_training_id)
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return UserTraining[] Returns an array of UserTraining objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserTraining
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
