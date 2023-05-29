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
    private CalendarioController $calendarioController;

    public function __construct(
        SerializerInterface $serializer,
        FestivoNacionalRepository $festivoNacionalRepository,
        CalendarioController $calendarioController
    )
    {
        $this->serializer = $serializer;
        $this->festivoNacionalRepository = $festivoNacionalRepository;
        $this->calendarioController = $calendarioController;
    }

    public function getFestivosNacionales(): array
    {
        [$anioAc, $anioSig] = $this->calendarioController->calcularAnios();

        $anio = substr($anioAc, 2, 3);
        $anioSiguiente = substr($anioSig, 2, 3);

        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosNacionales.json');
        $festivosArray = json_decode($festivosJson, true);
        $arrayNombreFestivos = [];

        foreach ($festivosArray['festivosNacionales-España'] as &$festivo) {
            $festivo['inicio'] = str_replace('%AN%', $anio, $festivo['inicio']);
            $festivo['inicio'] = str_replace('%AC%', $anioSiguiente, $festivo['inicio']);
            $festivo['final'] = str_replace('%AN%', $anio, $festivo['final']);
            $festivo['final'] = str_replace('%AC%', $anioSiguiente, $festivo['final']);
            array_push($arrayNombreFestivos, $festivo['nombre']);
        }

        $festivos = $this->serializer->denormalize($festivosArray['festivosNacionales-España'], 'App\Entity\FestivoNacional[]');

        foreach ($festivos as $festivoNacional) {
            if(!$this->festivoNacionalRepository->findOneFecha($festivoNacional->getInicio())) {
                $this->festivoNacionalRepository->save($festivoNacional,true);
            }
            //Buscamos los festivos que tengan dias intermedios y no sean acerca de cuatrimestres (inicios y finales de cuatrimestres)
            if($festivoNacional->getInicio() != $festivoNacional->getFinal()) {
                self::completaFestivosCentroIntermedios($festivoNacional);
            }
        }
        return $arrayNombreFestivos;
    }

    public function completaFestivosCentroIntermedios($festivoNacional): void
    {
        $inicio = \DateTime::createFromFormat('d-m-y', $festivoNacional->getInicio());
        $final = \DateTime::createFromFormat('d-m-y', $festivoNacional->getFinal());

        while ($inicio < $final) {
            //Creamos nuestro festivoIntermedio
            $festivoIntermedio = new FestivoNacional();
            $festivoIntermedio->setNombre($festivoNacional->getNombre());
            if(!is_null($festivoNacional->getAbreviatura())) {
                $festivoIntermedio->setAbreviatura($festivoNacional->getAbreviatura());
            }
            $festivoIntermedio->setFinal($festivoNacional->getFinal());
            //Añadimos un día al inicio
            $inicio->add(new \DateInterval('P1D')); 
            $festivoIntermedio->setInicio($inicio->format('j-n-y'));
            $this->festivoNacionalRepository->save($festivoIntermedio,true);
        }
    }
}
