<?php

namespace App\Repository;

use App\Entity\Dia;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dia>
 *
 * @method Dia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dia[]    findAll()
 * @method Dia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiaRepository extends ServiceEntityRepository
{
    /**
     * Lo que hace la funcion
     * @param $registry esto es ...
     * @return 
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dia::class);
    }

    public function save(Dia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(){
        $this->getEntityManager()->flush();
    }

    public function remove(Dia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Dia[] Returns an array of Dia objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('d')
//            ->andWhere('d.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('d.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneByFecha($fecha, $calendarioId): ?Dia
    {
        return $this->createQueryBuilder('d')
            ->join('App\Entity\Mes','mes','WITH','mes.id = d.mes')
            ->join('App\Entity\Anio','an','WITH','mes.anio = an.id')
            ->andWhere('d.fecha = :fecha')
            ->andWhere('an.calendario = :calendarioId')
            ->setParameter('fecha', $fecha)
            ->setParameter('calendarioId', $calendarioId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
