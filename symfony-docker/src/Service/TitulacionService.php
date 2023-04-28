<?php

namespace App\Service;

use App\Repository\TitulacionRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON clase y persistirlo en la base de datos junto con su titulacion.
 */
class TitulacionService
{
    private SerializerInterface $serializer;
    private TitulacionRepository $titulacionRepository;

    public function __construct(
        SerializerInterface $serializer,
        TitulacionRepository $titulacionRepository
    )
    {
        $this->serializer = $serializer;
        $this->titulacionRepository = $titulacionRepository;
    }

    public function getTitulaciones(): array
    {
        $asignaturasJson = file_get_contents(__DIR__ . '/../resources/asignaturas.json');
        $asignaturasArray = json_decode($asignaturasJson, true);

        //Array de objetos Titulacion que devuelve el metodo
        $titulaciones = [];

        foreach ($asignaturasArray['asignaturas'] as $asignatura) {
            $titulacion = $this->serializer->denormalize($asignatura, 'App\Entity\Titulacion');
            if(!$this->titulacionRepository->findOneBynombreTitulacion($titulacion->getNombreTitulacion())){
                $this->titulacionRepository->save($titulacion,true);
                $titulaciones[] = $titulacion;
            } else {
                //En caso de titulacion repetida, se meterá en el array la anterior
                $titulaciones[] = $this->titulacionRepository->findOneBynombreTitulacion($titulacion->getNombreTitulacion());
            }
        }

        return $titulaciones;
    }
}
