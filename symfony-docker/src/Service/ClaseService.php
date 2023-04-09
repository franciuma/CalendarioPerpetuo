<?php

namespace App\Service;

use App\Repository\ClaseRepository;
use App\Entity\Calendario;
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
        ClaseRepository $claseRepository
    )
    {
        $this->serializer = $serializer;
        $this->claseRepository = $claseRepository;
    }

    public function getClases(Calendario $calendario): void
    {
        $clasesJson = file_get_contents(__DIR__ . '/../resources/clases.json');
        $clasesArray = json_decode($clasesJson, true);

        $clases = $this->serializer->denormalize($clasesArray['clases'], 'App\Entity\Clase[]');

        foreach ($clases as $clase) {
            $clase->setCalendario($calendario);
            $this->claseRepository->save($clase,true);
        }
    }
}
