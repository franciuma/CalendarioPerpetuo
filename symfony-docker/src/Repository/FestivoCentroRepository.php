<?php

namespace App\Repository;

use App\Entity\FestivoCentro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FestivoCentro>
 *
 * @method FestivoCentro|null find($id, $lockMode = null, $lockVersion = null)
 * @method FestivoCentro|null findOneBy(array $criteria, array $orderBy = null)
 * @method FestivoCentro[]    findAll()
 * @method FestivoCentro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FestivoCentroRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FestivoCentro::class);
    }

    public function save(FestivoCentro $entity, bool $flush = false): void
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

    public function remove(FestivoCentro $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeFestivosCentro(array $festivosCentro): void
    {
        foreach ($festivosCentro as $festivoCentro) {
            $this->getEntityManager()->remove($festivoCentro);
        }
    }

    /**
     * @return FestivoCentro[] Returns an array of FestivoCentro objects
     */
    public function findByCentroId($centroId): array
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.centro = :val')
            ->setParameter('val', $centroId)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneFechaCentro($fecha, $centroId): ?FestivoCentro
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.inicio = :fecha')
            ->andWhere('f.centro = :centro')
            ->setParameter('fecha', $fecha)
            ->setParameter('centro',$centroId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneFechaInicioFinal($inicio, $final): ?FestivoCentro
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

    public function findOneFechaFinalCentro($fecha, $centroId): ?FestivoCentro
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.final = :fecha')
            ->andWhere('f.centro = :centro')
            ->andWhere('f.nombre LIKE :nombre')
            ->setParameter('fecha', $fecha)
            ->setParameter('centro',$centroId)
            ->setParameter('nombre', '%cuatrimestre%')
            ->orderBy('f.nombre', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function removeByNombreCentro($nombre, $centro)
    {
        $query = $this->createQueryBuilder('f')
            ->delete(FestivoCentro::class, 'fc')
            ->where('fc.nombre = :nombre')
            ->andWhere('fc.centro = :centro')
            ->setParameter('nombre', $nombre)
            ->setParameter('centro', $centro)
            ->getQuery();

        $query->execute();
    }

    public function obtenerids($nombre)
    {
        $query = $this->createQueryBuilder('fc')
            ->select('fc.id')
            ->where('fc.nombre = :nombre')
            ->setParameter('nombre', $nombre)
            ->getQuery()
            ->getResult()
            ;

        $result = array_column($query, 'id');
        return $result;
    }
}
