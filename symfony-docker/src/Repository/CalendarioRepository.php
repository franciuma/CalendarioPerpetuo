<?php

namespace App\Repository;

use App\Entity\Calendario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Calendario>
 *
 * @method Calendario[]    findAll()
 * @method Calendario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method Calendario|null findOneByNombre(array $criteria, array $orderBy = null)
 * @method Calendario|null findOneByProvincia(array $criteria, array $orderBy = null)
 */
class CalendarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Calendario::class);
    }

    public function save(Calendario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Calendario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Calendario[] Returns an array of Calendario objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneByProfesor($profesorId): ?Calendario
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.profesor = :val')
            ->setParameter('val', $profesorId)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }
}
