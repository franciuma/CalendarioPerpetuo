<?php

namespace App\Repository;

use App\Entity\UsuarioGrupo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsuarioGrupo>
 *
 * @method UsuarioGrupo|null find($id, $lockMode = null, $lockVersion = null)
 * @method UsuarioGrupo|null findOneBy(array $criteria, array $orderBy = null)
 * @method UsuarioGrupo[]    findAll()
 * @method UsuarioGrupo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioGrupoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsuarioGrupo::class);
    }

    public function save(UsuarioGrupo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function removeUsuarioGrupos(array $usuarioGrupos): void
    {
        foreach ($usuarioGrupos as $usuarioGrupo) {
            $this->getEntityManager()->remove($usuarioGrupo);
            $this->getEntityManager()->flush();
        }
    }

    public function remove(UsuarioGrupo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return UsuarioGrupo[] Returns an array of UsuarioGrupo objects
     */
    public function findUsuarioGrupoByUsuarioId($usuarioId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.usuario = :val')
            ->setParameter('val', $usuarioId)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByGrupoId($grupoId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.grupo = :val')
            ->setParameter('val', $grupoId)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByUsuarioGrupo($usuarioId, $grupoId): ?UsuarioGrupo
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.grupo = :grupo')
            ->andWhere('p.usuario = :usuario')
            ->setParameter('grupo', $grupoId)
            ->setParameter('usuario', $usuarioId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

//    public function findOneBySomeField($value): ?UsuarioGrupo
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
