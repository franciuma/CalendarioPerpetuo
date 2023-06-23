<?php

namespace App\Service;

use App\Repository\AsignaturaRepository;
use App\Repository\LeccionRepository;
use App\Repository\TitulacionRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON clase y persistirlo en la base de datos.
 */
class LeccionService
{
    private SerializerInterface $serializer;
    private LeccionRepository $leccionRepository;
    private AsignaturaRepository $asignaturaRepository;
    private TitulacionRepository $titulacionRepository;

    public function __construct(
        SerializerInterface $serializer,
        LeccionRepository $leccionRepository,
        AsignaturaRepository $asignaturaRepository,
        TitulacionRepository $titulacionRepository
    )
    {
        $this->serializer = $serializer;
        $this->leccionRepository = $leccionRepository;
        $this->asignaturaRepository = $asignaturaRepository;
        $this->titulacionRepository = $titulacionRepository;
    }

    public function getLecciones(): void
    {
        $asignaturasJson = file_get_contents(__DIR__ . '/../resources/asignaturas.json');
        $asignaturasArray = json_decode($asignaturasJson, true);

        foreach ($asignaturasArray['asignaturas'] as $asignatura) {
            $leccionesAsignatura = $this->serializer->denormalize($asignatura['lecciones'], 'App\Entity\Leccion[]');
            $titulacionDividida = explode("-",$asignatura["nombreTitulacion"]);

            foreach ($leccionesAsignatura as $leccion) {
                $titulacionObjeto = $this->titulacionRepository->findOneByAbreviaturaProvincia($titulacionDividida[0], $titulacionDividida[1]);
                $asignaturaLeccion = $this->asignaturaRepository->findOneByNombreTitulacion($asignatura['nombre'], $titulacionObjeto->getId());
                $leccion->setAsignatura($asignaturaLeccion);
                $this->leccionRepository->save($leccion);
            }
        }
        $this->leccionRepository->flush();
    }
}
