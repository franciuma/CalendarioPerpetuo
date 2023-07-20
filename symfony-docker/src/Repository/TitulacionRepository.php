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

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Titulacion $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllTitulacionCentro(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.id, t.nombreTitulacion, c.nombre')
            ->innerJoin('t.centro', 'c')
            ->getQuery()
            ->getResult();
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

    public function findOneByAbreviaturaProvincia($abreviatura, $provincia): ?Titulacion
        {
            return $this->createQueryBuilder('t')
                ->join('App\Entity\Centro','c','WITH','c.id = t.centro')
                ->andWhere('t.abreviatura = :abreviatura')
                ->andWhere('c.provincia = :provincia')
                ->setParameter('abreviatura', $abreviatura)
                ->setParameter('provincia', $provincia)
                ->getQuery()
                ->getOneOrNullResult()
            ;
        }

    public function findOneBynombreTitulacion($nombreTitulacion): ?Titulacion
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.nombreTitulacion = :val')
            ->setParameter('val', $nombreTitulacion)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findNombreBynombreTitulacion($nombreTitulacion): ?Titulacion
    {
        return $this->createQueryBuilder('t')
            ->select('t.nombreTitulacion')
            ->andWhere('t.nombreTitulacion = :val')
            ->setParameter('val', $nombreTitulacion)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllNombre(): array
    {
        $resultado = $this->createQueryBuilder('t')
        ->select('t.nombreTitulacion')
        ->getQuery()
        ->getResult()        
    ;

        return array_map(static function($titulacion) {
            return $titulacion['nombreTitulacion'];
        }, $resultado);
    }
}
