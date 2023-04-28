<?php

namespace App\Repository;

use App\Entity\Titulacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Titulacion>
 *
 * @method Titulacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Titulacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Titulacion[]    findAll()
 * @method Titulacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TitulacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Titulacion::class);
    }

    public function save(Titulacion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Titulacion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Titulacion[] Returns an array of Titulacion objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneBynombreTitulacion($nombreTitulacion): ?Titulacion
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.nombreTitulacion = :val')
            ->setParameter('val', $nombreTitulacion)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
