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

    public function getClases(): void
    {
        $clasesJson = file_get_contents(__DIR__ . '/../resources/clases.json');
        $clasesArray = json_decode($clasesJson, true);

        $clases = $this->serializer->denormalize($clasesArray['clases'], 'App\Entity\Clase[]');

        foreach ($clases as $clase) {
            if(!$this->claseRepository->findOneByNombre($clase->getNombre())) {
                $this->claseRepository->save($clase,true);
            }
        }
    }
}
