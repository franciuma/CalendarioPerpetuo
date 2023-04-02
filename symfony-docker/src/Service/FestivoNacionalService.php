<?php

namespace App\Service;

use App\Repository\FestivoNacionalRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\CalendarioController;

/**
 * Clase utilizada para traducir JSON festivoNacional y persistirlo en la base de datos.
 */
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
        $anio = substr(CalendarioController::ANIO, 2, 3);
        $anioSiguiente = substr(CalendarioController::ANIO_SIGUIENTE, 2, 3);

        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosNacionales.json');
        $festivosArray = json_decode($festivosJson, true);

        foreach ($festivosArray['festivosGlobales'] as &$festivo) {
            $festivo['inicio'] = str_replace('%AN%', $anio, $festivo['inicio']);
            $festivo['inicio'] = str_replace('%AC%', $anioSiguiente, $festivo['inicio']);
            $festivo['final'] = str_replace('%AN%', $anio, $festivo['final']);
            $festivo['final'] = str_replace('%AC%', $anioSiguiente, $festivo['final']);
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
