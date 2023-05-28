<?php

namespace App\Service;

use App\Entity\Centro;
use App\Repository\CentroRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON centro y persistirlo en la base de datos.
 */
class CentroService
{
    private SerializerInterface $serializer;
    private CentroRepository $centroRepository;

    public function __construct(
        SerializerInterface $serializer,
        CentroRepository $centroRepository
    )
    {
        $this->serializer = $serializer;
        $this->centroRepository = $centroRepository;
    }

    public function getCentro(): Centro
    {
        $centroJson = file_get_contents(__DIR__ . '/../resources/centro.json');
        $centroArray = json_decode($centroJson, true);

        $centro = $this->serializer->denormalize($centroArray['centro'][0], 'App\Entity\Centro');

        // Si el centro no está en la bd
        if(is_null($this->centroRepository->findOneByProvinciaCentro($centro->getProvincia(), $centro->getNombre()))){
            $this->centroRepository->save($centro,true);
        } else {
            $centro = $this->centroRepository->findOneByProvinciaCentro($centro->getProvincia(), $centro->getNombre());
        }

        return $centro;
    }
}
