<?php

namespace App\Repository;

use App\Entity\Asignatura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Asignatura>
 *
 * @method Asignatura|null find($id, $lockMode = null, $lockVersion = null)
 * @method Asignatura|null findOneBy(array $criteria, array $orderBy = null)
 * @method Asignatura[]    findAll()
 * @method Asignatura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AsignaturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Asignatura::class);
    }

    public function save(Asignatura $entity, bool $flush = false): void
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

    public function remove(Asignatura $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Asignatura[] Returns an array of Asignatura objects
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

    public function findOneByNombre($nombre): ?Asignatura
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.nombre = :val')
            ->setParameter('val', $nombre)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByNombreTitulacion($nombre, $titulacionId): ?Asignatura
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.nombre = :val')
            ->andWhere('a.titulacion = :valo')
            ->setParameter('val', $nombre)
            ->setParameter('valo', $titulacionId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllNombre(): array
    {
        $resultado = $this->createQueryBuilder('a')
        ->select('a.nombre')
        ->getQuery()
        ->getResult()
    ;

        return array_map(static function($asignatura) {
            return $asignatura['nombre'];
        }, $resultado);
    }
}
