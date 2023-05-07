<?php

namespace App\Repository;

use App\Entity\Clase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Clase>
 *
 * @method Clase|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clase|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clase[]    findAll()
 * @method Clase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clase::class);
    }

    public function save(Clase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Clase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Clase[] Returns an array of Clase objects
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

    public function findOneFecha($fecha,$calendarioId): ?Clase //DEPENDIENDO, SE LE PASARA CALENDARIO, O CENTRO Y PROVINCIA
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.fecha = :val')
            ->andWhere('c.calendario = :valo')
            ->setParameter('val', $fecha)
            ->setParameter('valo', $calendarioId)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findOneByNombre($nombre): ?Clase
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.nombre = :val')
            ->setParameter('val', $nombre)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByCalendario($calendarioId): ?Clase
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.calendario = :val')
            ->setParameter('val', $calendarioId)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }
}
