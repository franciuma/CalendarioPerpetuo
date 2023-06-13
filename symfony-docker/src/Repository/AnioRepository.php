<?php

namespace App\Repository;

use App\Entity\Anio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Anio>
 *
 * @method Anio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Anio[]    findAll()
 * @method Anio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Anio|null findOneBynumAnio(array $criteria, array $orderBy = null)
 */
class AnioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Anio::class);
    }

    public function save(Anio $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Anio $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Anio[] Returns an array of Anio objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneByNumAnio($anio): ?Anio
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.numAnio = :val')
            ->setParameter('val', $anio)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }
}
