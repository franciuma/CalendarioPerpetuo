<?php

namespace App\Service;

use App\Repository\AsignaturaRepository;
use App\Repository\LeccionRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON clase y persistirlo en la base de datos.
 */
class LeccionService
{
    private SerializerInterface $serializer;
    private LeccionRepository $leccionRepository;
    private AsignaturaRepository $asignaturaRepository;

    public function __construct(
        SerializerInterface $serializer,
        LeccionRepository $leccionRepository,
        AsignaturaRepository $asignaturaRepository
    )
    {
        $this->serializer = $serializer;
        $this->leccionRepository = $leccionRepository;
        $this->asignaturaRepository = $asignaturaRepository;
    }

    public function getLecciones(): void
    {
        $asignaturasJson = file_get_contents(__DIR__ . '/../resources/asignaturas.json');
        $asignaturasArray = json_decode($asignaturasJson, true);

        foreach ($asignaturasArray['asignaturas'] as $asignatura) {
            $leccionesAsignatura = $this->serializer->denormalize($asignatura['lecciones'], 'App\Entity\Leccion[]');
            foreach ($leccionesAsignatura as $leccion) {
                $asignaturaLeccion = $this->asignaturaRepository->findOneByNombre($asignatura['nombre']);
                $leccion->setAsignatura($asignaturaLeccion);
                $this->leccionRepository->save($leccion,true);
            }
        }
    }
}
