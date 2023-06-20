<?php

namespace App\Repository;

use App\Entity\Grupo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Grupo>
 *
 * @method Grupo|null find($id, $lockMode = null, $lockVersion = null)
 * @method Grupo|null findOneBy(array $criteria, array $orderBy = null)
 * @method Grupo[]    findAll()
 * @method Grupo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GrupoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Grupo::class);
    }

    public function save(Grupo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Grupo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    public function removeGrupos(array $grupos): void
    {
        foreach ($grupos as $grupo) {
            $this->getEntityManager()->remove($grupo);
        }
    }

    /**
     * @return Grupo[] Returns an array of Grupo objects
     */
    public function findByTitulacionId($titulacionId): array
    {
        return $this->createQueryBuilder('g')
            ->join('App\Entity\Asignatura','a','WITH','g.asignatura = a.id')
            ->join('App\Entity\Titulacion','t','WITH','a.titulacion = t.id')
            ->andWhere('t.id = :val')
            ->setParameter('val', $titulacionId)
            ->orderBy('g.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneById($grupoId): ?Grupo
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.id = :val')
            ->setParameter('val', $grupoId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByAsigLetraHorario($asignaturaId, $letra, $horario): Grupo
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.asignatura = :asig')
            ->andWhere('g.letra = :letra')
            ->andWhere('g.horario = :horario')
            ->setParameter('asig', $asignaturaId)
            ->setParameter('letra', $letra)
            ->setParameter('horario', $horario)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
