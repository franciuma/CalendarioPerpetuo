<?php

namespace App\Service;

use App\Repository\FestivoCentroRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\CalendarioController;
use App\Entity\Centro;

/**
 * Clase utilizada para traducir JSON festivoLocales y persistirlo en la base de datos.
 */
class FestivoCentroService
{
    private SerializerInterface $serializer;
    private FestivoCentroRepository $festivoCentroRepository;

    public function __construct(
        SerializerInterface $serializer,
        FestivoCentroRepository $festivoCentroRepository,
    )
    {
        $this->serializer = $serializer;
        $this->festivoCentroRepository = $festivoCentroRepository;
    }

    public function getFestivosCentro(Centro $centro): array
    {
        $anio = substr(CalendarioController::ANIO, 2, 3);
        $anioSiguiente = substr(CalendarioController::ANIO_SIGUIENTE, 2, 3);
        $nombreCentro = $centro->getNombre();

        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosCentro.json');
        $festivosArray = json_decode($festivosJson, true);

        foreach ($festivosArray['festivosCentro'.$nombreCentro] as &$festivo) {
            $festivo['inicio'] = str_replace('%AN%', $anio, $festivo['inicio']);
            $festivo['inicio'] = str_replace('%AC%', $anioSiguiente, $festivo['inicio']);
            $festivo['final'] = str_replace('%AN%', $anio, $festivo['final']);
            $festivo['final'] = str_replace('%AC%', $anioSiguiente, $festivo['final']);
        }

        $festivos = $this->serializer->denormalize($festivosArray['festivosCentro'.$nombreCentro], 'App\Entity\FestivoCentro[]');

        foreach ($festivos as $festivoCentro) {
            if(!$this->festivoCentroRepository->findOneFecha($festivoCentro->getInicio())) {
                $festivoCentro->setCentro($centro);
                $this->festivoCentroRepository->save($festivoCentro,true);
            }
        }

        return $festivos;
    }
}
