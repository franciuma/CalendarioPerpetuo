<?php

namespace App\Service;

use App\Repository\AsignaturaRepository;
use App\Repository\TitulacionRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON clase y persistirlo en la base de datos junto con su titulacion.
 */
class AsignaturaService
{
    private SerializerInterface $serializer;
    private AsignaturaRepository $asignaturaRepository;
    private TitulacionRepository $titulacionRepository;

    public function __construct(
        SerializerInterface $serializer,
        AsignaturaRepository $asignaturaRepository,
        TitulacionRepository $titulacionRepository
    )
    {
        $this->serializer = $serializer;
        $this->asignaturaRepository = $asignaturaRepository;
        $this->titulacionRepository = $titulacionRepository;
    }

    public function getAsignaturas(): void
    {
        $asignaturasJson = file_get_contents(__DIR__ . '/../resources/asignaturas.json');
        $asignaturasArray = json_decode($asignaturasJson, true);

        foreach ($asignaturasArray['asignaturas'] as $asignatura) {
            $titulacion = $asignatura["nombreTitulacion"];
            $titulacionDividida = explode("-",$titulacion);
            $asignaturaObjeto = $this->serializer->denormalize($asignatura, 'App\Entity\Asignatura');

            if(!$this->asignaturaRepository->findOneByNombre($asignaturaObjeto->getNombre())){
                //Buscamos la titulación
                $titulacionObjeto = $this->titulacionRepository->findOneByAbreviaturaProvincia($titulacionDividida[0], $titulacionDividida[1]);
                $asignaturaObjeto->setTitulacion($titulacionObjeto);
                $this->asignaturaRepository->save($asignaturaObjeto,true);
            }
        }
    }
}
