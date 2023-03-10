<?php

namespace App\Service;

use App\Repository\FestivoNacionalRepository;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

class FestivoNacionalService
{
    private SerializerInterface $serializer;
    private FestivoNacionalRepository $festivoNacionalRepository;

    public function __construct(SerializerInterface $serializer, FestivoNacionalRepository $festivoNacionalRepository)
    {
        $this->serializer = $serializer;
        $this->festivoNacionalRepository = $festivoNacionalRepository;
    }

    public function getFestivosNacionales(): array
    {
        $anio = date('Y');
        $anioActual = substr($anio, 2, 3);
        $anioAnterior = substr($anio, 2, 3)-1;

        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosNacionales.json');
        $festivosArray = json_decode($festivosJson, true);

            foreach ($festivosArray['festivosGlobales'] as &$festivo) {
                $festivo['inicio'] = str_replace('%AN%', $anioAnterior, $festivo['inicio']);
                $festivo['inicio'] = str_replace('%AC%', $anioActual, $festivo['inicio']);
                $festivo['final'] = str_replace('%AN%', $anioAnterior, $festivo['final']);
                $festivo['final'] = str_replace('%AC%', $anioActual, $festivo['final']);
            }

            $festivos = $this->serializer->denormalize($festivosArray['festivosGlobales'], 'App\Entity\FestivoNacional[]');

            foreach ($festivos as $festivoNacional) {
                if(!$this->festivoNacionalRepository->findOneFecha($festivoNacional->getInicio())) {
                    $this->festivoNacionalRepository->save($festivoNacional,true);
                }
            }

        return $festivos;
    }
}
