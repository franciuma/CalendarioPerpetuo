<?php

namespace App\Service;

use App\Repository\ClaseRepository;
use App\Repository\AsignaturaRepository;
use App\Entity\Calendario;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON clase y persistirlo en la base de datos.
 */
class ClaseService
{
    private SerializerInterface $serializer;
    private ClaseRepository $claseRepository;
    private AsignaturaRepository $asignaturaRepository;

    public function __construct(
        SerializerInterface $serializer,
        ClaseRepository $claseRepository,
        AsignaturaRepository $asignaturaRepository
    )
    {
        $this->serializer = $serializer;
        $this->claseRepository = $claseRepository;
        $this->asignaturaRepository = $asignaturaRepository;
    }

    public function getClases(Calendario $calendario): void
    {
        $clasesJson = file_get_contents(__DIR__ . '/../resources/clases.json');
        $clasesArray = json_decode($clasesJson, true);

        foreach ($clasesArray['clases'] as $claseDatos) {
            $clase = $this->serializer->denormalize($claseDatos, 'App\Entity\Clase');
            $asignaturaNombre = $claseDatos['asignaturaNombre'];
            $asignatura = $this->asignaturaRepository->findOneByNombre($asignaturaNombre);
            $clase->setAsignatura($asignatura);
            $clase->setCalendario($calendario);
            $this->claseRepository->save($clase,true);
        }
    }
}
