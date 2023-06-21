<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Usuario>
 *
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    public function save(Usuario $entity, bool $flush = false): void
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

    public function remove(Usuario $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findGruposByUsuario($nombre, $apellidoPr, $apellidoSeg)
    {
        return $this->createQueryBuilder('u')
            ->select('g')
            ->join('App\Entity\UsuarioGrupo','ug','WITH','u.id = ug.usuario')
            ->join('App\Entity\Grupo','g','WITH','ug.grupo = g.id')
            ->where('u.nombre = :nombre')
            ->andWhere('u.primerApellido = :apellidoPr')
            ->andWhere('u.segundoApellido = :apellidoSeg')
            ->setParameter('nombre', $nombre)
            ->setParameter('apellidoPr', $apellidoPr)
            ->setParameter('apellidoSeg', $apellidoSeg)
            ->getQuery()
            ->getResult();
    }

    public function findGruposByUsuarioId($usuarioId): array
    {
        return $this->createQueryBuilder('u')
            ->select('g')
            ->join('App\Entity\UsuarioGrupo','ug','WITH','u.id = ug.usuario')
            ->join('App\Entity\Grupo','g','WITH','ug.grupo = g.id')
            ->where('u.id = :usuarioid')
            ->setParameter('usuarioid', $usuarioId)
            ->getQuery()
            ->getResult();
    }

    public function findOneByNombreApellidos($nombreUsuario, $apellidoPr, $apellidoSeg): ?Usuario
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.nombre = :nombre')
            ->andWhere('u.primerApellido = :apellidoPr')
            ->andWhere('u.segundoApellido = :apellidoSeg')
            ->setParameter('nombre', $nombreUsuario)
            ->setParameter('apellidoPr', $apellidoPr)
            ->setParameter('apellidoSeg', $apellidoSeg)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneByNombre($nombre): ?Usuario
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.nombre = :val')
            ->setParameter('val', $nombre)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneById($usuarioId): ?Usuario
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :val')
            ->setParameter('val', $usuarioId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findAllProfesores(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.tipo = :val')
            ->setParameter('val', 'Profesor')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllAlumnos(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.tipo = :val')
            ->setParameter('val', 'Alumno')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findAllProfesoresConCalendario(): array
    {
        return $this->createQueryBuilder('u')
            ->join('App\Entity\Calendario','c','WITH','u.id = c.usuario')
            ->andWhere('u.tipo = :val')
            ->setParameter('val', 'Profesor')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByDni($dni): ?Usuario
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.dni = :val')
            ->setParameter('val', $dni)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
//    /**
//     * @return Usuario[] Returns an array of Usuario objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Usuario
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
