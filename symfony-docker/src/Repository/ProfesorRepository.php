<?php

namespace App\Repository;

use App\Entity\Profesor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Profesor>
 *
 * @method Profesor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Profesor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Profesor[]    findAll()
 * @method Profesor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProfesorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Profesor::class);
    }

    public function save(Profesor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Profesor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findGruposByProfesor($nombre, $apellidoPr, $apellidoSeg)
    {
        return $this->createQueryBuilder('p')
            ->select('g')
            ->join('App\Entity\ProfesorGrupo','pg','WITH','p.id = pg.profesor')
            ->join('App\Entity\Grupo','g','WITH','pg.grupo = g.id')
            ->where('p.nombre = :nombre')
            ->andWhere('p.primerApellido = :apellidoPr')
            ->andWhere('p.segundoApellido = :apellidoSeg')
            ->setParameter('nombre', $nombre)
            ->setParameter('apellidoPr', $apellidoPr)
            ->setParameter('apellidoSeg', $apellidoSeg)
            ->getQuery()
            ->getResult();
    }


//    /**
//     * @return Profesor[] Returns an array of Profesor objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

    public function findOneByNombre($nombreProfesor): ?Profesor
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.nombre = :val')
            ->setParameter('val', $nombreProfesor)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
