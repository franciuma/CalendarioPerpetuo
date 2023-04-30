<?php

namespace App\Repository;

use App\Entity\FestivoCentro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FestivoCentro>
 *
 * @method FestivoCentro|null find($id, $lockMode = null, $lockVersion = null)
 * @method FestivoCentro|null findOneBy(array $criteria, array $orderBy = null)
 * @method FestivoCentro[]    findAll()
 * @method FestivoCentro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FestivoCentroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FestivoCentro::class);
    }

    public function save(FestivoCentro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FestivoCentro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FestivoCentro[] Returns an array of FestivoCentro objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneFecha($fecha): ?FestivoCentro
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.inicio = :val')
            ->setParameter('val', $fecha)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
