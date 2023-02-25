<?php

namespace App\Service;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class FestivoNacionalService
{
    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function getFestivosNacionales(): array
    {
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosNacionales.json');
        $festivosArray = json_decode($festivosJson, true);

        var_dump($festivosArray);exit();

        // Opcionalmente, puedes usar el Serializer para crear objetos
        $festivos = $this->serializer->denormalize($festivosArray, 'App\Entity\Festivo[]');

        return $festivos;
    }
}
