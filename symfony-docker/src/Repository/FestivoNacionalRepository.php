<?php

namespace App\Repository;

use App\Entity\FestivoNacional;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FestivoNacional>
 *
 * @method FestivoNacional|null find($id, $lockMode = null, $lockVersion = null)
 * @method FestivoNacional|null findOneBy(array $criteria, array $orderBy = null)
 * @method FestivoNacional[]    findAll()
 * @method FestivoNacional[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method FestivoNacional|null findOneFecha(array $criteria, array $orderBy = null)
 */
class FestivoNacionalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FestivoNacional::class);
    }

    public function save(FestivoNacional $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FestivoNacional $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FestivoNacional[] Returns an array of FestivoNacional objects
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

    public function findOneFecha($fecha): ?FestivoNacional
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.inicio = :val')
            ->setParameter('val', $fecha)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
