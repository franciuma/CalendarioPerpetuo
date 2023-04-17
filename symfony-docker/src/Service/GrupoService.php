<?php

namespace App\Service;

use App\Repository\GrupoRepository;
use App\Repository\AsignaturaRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON profesorGrupo y persistir grupos en la base de datos.
 */
class GrupoService
{
    private SerializerInterface $serializer;
    private GrupoRepository $grupoRepository;
    private AsignaturaRepository $asignaturaRepository;

    public function __construct(
        SerializerInterface $serializer,
        GrupoRepository $grupoRepository,
        AsignaturaRepository $asignaturaRepository
    )
    {
        $this->serializer = $serializer;
        $this->grupoRepository = $grupoRepository;
        $this->asignaturaRepository = $asignaturaRepository;
    }

    public function getGrupos(): void
    {
        $grupoJSON = file_get_contents(__DIR__ . '/../resources/profesorGrupo.json');
        $gruposArray = json_decode($grupoJSON, true);

        $gruposDatos = $gruposArray['grupos'];

        foreach ($gruposDatos as $grupoDatos) {
            $asignaturaNombre = $grupoDatos['asignaturaNombre'];
            $grupo = $this->serializer->denormalize($grupoDatos, 'App\Entity\Grupo');
            $asignatura = $this->asignaturaRepository->findOneByNombre($asignaturaNombre);
            $grupo->setAsignatura($asignatura);
            $this->grupoRepository->save($grupo,true);
        }
    }
}
