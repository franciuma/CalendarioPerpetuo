<?php

namespace App\Repository;

use App\Entity\Evento;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Evento>
 *
 * @method Evento|null find($id, $lockMode = null, $lockVersion = null)
 * @method Evento|null findOneBy(array $criteria, array $orderBy = null)
 * @method Evento[]    findAll()
 * @method Evento[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Evento::class);
    }

    public function save(Evento $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Evento $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function removeEventos(array $eventos): void
    {
        foreach ($eventos as $evento) {
            $this->getEntityManager()->remove($evento);
        }
    }

    public function findByClaseId($claseId): ?Evento
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.clase = :val')
            ->setParameter('val', $claseId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByAsignatura($asignaturaId): array
    {
        return $this->createQueryBuilder('e')
            ->join('App\Entity\Clase','cl','WITH','cl.id = e.clase')
            ->andWhere('cl.asignatura = :val')
            ->setParameter('val', $asignaturaId)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Evento[] Returns an array of Evento objects
     */
    public function findEventoClaseByCalendario($calendarioId): array
    {
        return $this->createQueryBuilder('e')
            ->join('App\Entity\Clase','cl','WITH','cl.id = e.clase')
            ->andWhere('cl.calendario = :val')
            ->setParameter('val', $calendarioId)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function removeByFestivoNacionalId($festivoNacionalId)
    {
        $query = $this->createQueryBuilder('e')
            ->delete(Evento::class, 'e')
            ->where('e.festivoNacional = :festivoNacionalId')
            ->setParameter('festivoNacionalId', $festivoNacionalId)
            ->getQuery()
            ;

        $query->execute();
    }
}