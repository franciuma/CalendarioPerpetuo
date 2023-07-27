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

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(FestivoLocal $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeByNombreProvincia($nombre, $provincia)
    {
        $query = $this->createQueryBuilder('f')
            ->delete(FestivoLocal::class, 'fn')
            ->where('fn.nombre = :nombre')
            ->andWhere('fn.provincia = :provincia')
            ->setParameter('nombre', $nombre)
            ->setParameter('provincia', $provincia)
            ->getQuery();

        $query->execute();
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

    public function findOneFecha($fecha): ?FestivoLocal
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.inicio = :val')
            ->setParameter('val', $fecha)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findOneFechaInicioFinal($inicio, $final): ?FestivoLocal
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.inicio = :inicio')
            ->andWhere('f.final = :final')
            ->setParameter('inicio', $inicio)
            ->setParameter('final', $final)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findOneFechaProvincia($fecha, $provincia): ?FestivoLocal
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.inicio = :fecha')
            ->andWhere('f.provincia = :provincia')
            ->setParameter('fecha', $fecha)
            ->setParameter('provincia', $provincia)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function obtenerids($nombre)
    {
        $query = $this->createQueryBuilder('fl')
            ->select('fl.id')
            ->where('fl.nombre = :nombre')
            ->setParameter('nombre', $nombre)
            ->getQuery()
            ->getResult()
            ;

        $result = array_column($query, 'id');
        return $result;
    }
}
