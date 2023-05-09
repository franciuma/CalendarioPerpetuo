<?php

namespace App\Service;

use App\Repository\FestivoNacionalRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\CalendarioController;
use App\Entity\FestivoNacional;

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
            //Buscamos los festivos que tengan dias intermedios y no sean acerca de cuatrimestres (inicios y finales de cuatrimestres)
            if($festivoNacional->getInicio() != $festivoNacional->getFinal()) {
                self::completaFestivosCentroIntermedios($festivoNacional);
            }
        }

        return $festivos;
    }

    public function completaFestivosCentroIntermedios($festivoNacional): void
    {
        $inicio = \DateTime::createFromFormat('d-m-y', $festivoNacional->getInicio());
        $final = \DateTime::createFromFormat('d-m-y', $festivoNacional->getFinal());

        while ($inicio < $final) {
            //Creamos nuestro festivoIntermedio
            $festivoIntermedio = new FestivoNacional();
            $festivoIntermedio->setNombre($festivoNacional->getNombre());
            $festivoIntermedio->setAbreviatura($festivoNacional->getAbreviatura());
            $festivoIntermedio->setFinal($festivoNacional->getFinal());
            //Añadimos un día al inicio
            $inicio->add(new \DateInterval('P1D')); 
            $festivoIntermedio->setInicio($inicio->format('j-n-y'));
            $this->festivoNacionalRepository->save($festivoIntermedio,true);
        }
    }
}
