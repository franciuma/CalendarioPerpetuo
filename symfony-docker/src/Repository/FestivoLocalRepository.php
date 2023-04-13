<?php

namespace App\Repository;

use App\Entity\FestivoLocal;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FestivoLocal>
 *
 * @method FestivoLocal|null find($id, $lockMode = null, $lockVersion = null)
 * @method FestivoLocal|null findOneBy(array $criteria, array $orderBy = null)
 * @method FestivoLocal[]    findAll()
 * @method FestivoLocal[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FestivoLocalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FestivoLocal::class);
    }

    public function save(FestivoLocal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FestivoLocal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FestivoLocal[] Returns an array of FestivoLocal objects
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
// ->andWhere(':val BETWEEN f.inicio AND f.final OR f.final = :val') para el tema de inicio y final
    public function findOneFecha($fecha): ?FestivoLocal
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.inicio = :val')
            ->setParameter('val', $fecha)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
