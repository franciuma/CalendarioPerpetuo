<?php

namespace App\Service;

use App\Entity\Asignatura;
use App\Repository\AsignaturaRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON clase y persistirlo en la base de datos.
 */
class AsignaturaService
{
    private SerializerInterface $serializer;
    private AsignaturaRepository $asignaturaRepository;

    public function __construct(
        SerializerInterface $serializer,
        AsignaturaRepository $asignaturaRepository
    )
    {
        $this->serializer = $serializer;
        $this->asignaturaRepository = $asignaturaRepository;
    }

    public function getAsignaturas(): void
    {
        $asignaturasJson = file_get_contents(__DIR__ . '/../resources/asignaturas.json');
        $asignaturasArray = json_decode($asignaturasJson, true);

        $asignaturas = $this->serializer->denormalize($asignaturasArray['asignaturas'], 'App\Entity\Asignatura[]');

        foreach ($asignaturas as $asignatura) {
            if(!$this->asignaturaRepository->findOneByNombre($asignatura->getNombre())){
                $this->asignaturaRepository->save($asignatura,true);
            }
        }
    }
}
