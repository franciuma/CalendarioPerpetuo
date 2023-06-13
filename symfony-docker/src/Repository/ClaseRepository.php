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

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Clase $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeClases(array $clases)
    {
        foreach ($clases as $clase) {
            $this->getEntityManager()->remove($clase);
        }
    }

    /**
     * @return Clase[] Returns an array of Clase objects
     */
    public function findByCalendario($calendarioId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.calendario = :val')
            ->setParameter('val', $calendarioId)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByFecha($fecha,$calendarioId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.fecha = :val')
            ->andWhere('c.calendario = :valo')
            ->setParameter('val', $fecha)
            ->setParameter('valo', $calendarioId)
            ->getQuery()
            ->getResult()
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

    public function findOneFechaAsignatura($fecha, $nombreAsignatura): ?Clase
    {
        return $this->createQueryBuilder('c')
            ->join('App\Entity\Asignatura','asig','WITH','c.asignatura = asig.id')
            ->andWhere('c.fecha = :val')
            ->andWhere('asig.nombre = :valo')
            ->setParameter('val', $fecha)
            ->setParameter('valo', $nombreAsignatura)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findClaseByFechaNombreGrupo($fecha, $nombre, $grupoId): ?Clase
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.fecha = :fecha')
            ->andWhere('c.nombre = :nombre')
            ->andWhere('c.grupo = :grupo')
            ->setParameter('fecha', $fecha)
            ->setParameter('nombre', $nombre)
            ->setParameter('grupo', $grupoId)
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult()
        ;
    }

    public function findByGrupoId($grupoId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.grupo = :grupo')
            ->setParameter('grupo', $grupoId)
            ->getQuery()
            ->getResult()
        ;
    }
}
