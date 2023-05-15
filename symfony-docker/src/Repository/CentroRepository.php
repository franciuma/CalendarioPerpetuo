<?php

namespace App\Repository;

use App\Entity\Centro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Centro>
 *
 * @method Centro|null find($id, $lockMode = null, $lockVersion = null)
 * @method Centro|null findOneBy(array $criteria, array $orderBy = null)
 * @method Centro[]    findAll()
 * @method Centro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CentroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Centro::class);
    }

    public function save(Centro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Centro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Centro[] Returns an array of Centro objects
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

    public function findOneByNombre($nombre): ?Centro
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.nombre = :nombre')
            ->setParameter('nombre', $nombre)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByProvinciaCentro($provincia, $centro): ?Centro
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.provincia = :provincia')
            ->andWhere('c.nombre = :centro')
            ->setParameter('provincia', $provincia)
            ->setParameter('centro', $centro)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByUsuario($usuarioId): ?Centro
    {
        return $this->createQueryBuilder('c')
        ->join('App\Entity\Calendario','ca','WITH','ca.centro = c.id')
        ->andWhere('ca.usuario = :usuario')
        ->setParameter('usuario', $usuarioId)
        ->getQuery()
        ->getOneOrNullResult()
    ;
    }

    public function findOneProvinciaByUsuario($usuarioId)
    {
        return $this->createQueryBuilder('c')
        ->join('App\Entity\Calendario','ca','WITH','ca.centro = c.id')
        ->andWhere('ca.usuario = :usuario')
        ->andWhere('c.nombre = :centro')
        ->setParameter('usuario', $usuarioId)
        ->getQuery()
        ->getOneOrNullResult()
    ;
    }
}
