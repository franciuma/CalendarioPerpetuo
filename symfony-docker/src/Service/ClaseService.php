<?php

namespace App\Service;

use App\Repository\ClaseRepository;
use App\Repository\AsignaturaRepository;
use App\Entity\Calendario;
use App\Repository\GrupoRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON clase y persistirlo en la base de datos.
 */
class ClaseService
{
    private SerializerInterface $serializer;
    private ClaseRepository $claseRepository;
    private AsignaturaRepository $asignaturaRepository;
    private GrupoRepository $grupoRepository;

    public function __construct(
        SerializerInterface $serializer,
        ClaseRepository $claseRepository,
        AsignaturaRepository $asignaturaRepository,
        GrupoRepository $grupoRepository
    )
    {
        $this->serializer = $serializer;
        $this->claseRepository = $claseRepository;
        $this->asignaturaRepository = $asignaturaRepository;
        $this->grupoRepository = $grupoRepository;
    }

    public function getClases(Calendario $calendario, $persistirBd): array
    {
        $clasesJson = file_get_contents(__DIR__ . '/../resources/clases.json');
        $clasesArray = json_decode($clasesJson, true);

        $clases = [];

        foreach ($clasesArray['clases'] as $claseDatos) {
            $clase = $this->serializer->denormalize($claseDatos, 'App\Entity\Clase');
            //Buscamos la asignatura asociada a la clase y la persistimos
            $asignaturaNombre = $claseDatos['asignaturaNombre'];
            $asignatura = $this->asignaturaRepository->findOneByNombre($asignaturaNombre);

            //Buscamos el grupo asociado a la clase y lo persistimos
            $grupoId = $claseDatos['grupo']['id'];
            $grupo = $this->grupoRepository->findOneById($grupoId);

            //Persistimos en clase si se requiere
            $clase->setAsignatura($asignatura);
            $clase->setGrupo($grupo);
            $clase->setCalendario($calendario);

            if($persistirBd == true) {
                $this->claseRepository->save($clase);
            }

            array_push($clases, $clase);
        }

        if($persistirBd == true) {
            $this->claseRepository->flush();
        }

        return $clases;
    }
}
