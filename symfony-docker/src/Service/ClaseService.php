<?php

namespace App\Service;

use App\Repository\ClaseRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON clase y persistirlo en la base de datos.
 */
class ClaseService
{
    private SerializerInterface $serializer;
    private ClaseRepository $claseRepository;

    public function __construct(
        SerializerInterface $serializer,
        ClaseRepository $claseRepository,
    )
    {
        $this->serializer = $serializer;
        $this->claseRepository = $claseRepository;
    }

    public function getClasesTeoricas(): void
    {
        $clasesJson = file_get_contents(__DIR__ . '/../resources/clases.json');
        $clasesArray = json_decode($clasesJson, true);

        $clasesTeoricas = $this->serializer->denormalize($clasesArray['teoricas'], 'App\Entity\Clase[]');

        foreach ($clasesTeoricas as $claseTeorica) {
            if(!$this->claseRepository->findOneByNombre($claseTeorica->getNombre())) {
                $claseTeorica->setTipoDeClase("Teorica");
                $this->claseRepository->save($claseTeorica,true);
            }
        }
    }

    public function getClasesPracticas(): void
    {
        $clasesJson = file_get_contents(__DIR__ . '/../resources/clases.json');
        $clasesArray = json_decode($clasesJson, true);

        $clasesPracticas = $this->serializer->denormalize($clasesArray['practicas'], 'App\Entity\Clase[]');

        foreach ($clasesPracticas as $clasePractica) {
            if(!$this->claseRepository->findOneByNombre($clasePractica->getNombre())) {
                $clasePractica->setTipoDeClase("Practica");
                $this->claseRepository->save($clasePractica,true);
            }
        }
    }
}
