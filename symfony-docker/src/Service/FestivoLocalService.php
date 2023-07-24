<?php

namespace App\Service;

use App\Repository\FestivoLocalRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\CalendarioController;
use App\Entity\Centro;
use App\Entity\FestivoLocal;

/**
 * Clase utilizada para traducir JSON festivoLocales y persistirlo en la base de datos.
 */
class FestivoLocalService
{
    private SerializerInterface $serializer;
    private FestivoLocalRepository $festivoLocalRepository;
    private CalendarioController $calendarioController;

    public function __construct(
        SerializerInterface $serializer,
        FestivoLocalRepository $festivoLocalRepository,
        CalendarioController $calendarioController
    )
    {
        $this->serializer = $serializer;
        $this->festivoLocalRepository = $festivoLocalRepository;
        $this->calendarioController = $calendarioController;
    }

    public function getFestivosLocales($provincia, $curso): array
    {
        $anio = substr($curso[0], 2, 3);
        $anioSiguiente = substr($curso[1], 2, 3);

        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosLocales.json');
        $festivosArray = json_decode($festivosJson, true);

        foreach ($festivosArray['festivosLocales'.$provincia] as &$festivo) {
            $festivo['inicio'] = str_replace('%AN%', $anio, $festivo['inicio']);
            $festivo['inicio'] = str_replace('%AC%', $anioSiguiente, $festivo['inicio']);
            $festivo['final'] = str_replace('%AN%', $anio, $festivo['final']);
            $festivo['final'] = str_replace('%AC%', $anioSiguiente, $festivo['final']);
        }

        $festivos = $this->serializer->denormalize($festivosArray['festivosLocales'.$provincia], 'App\Entity\FestivoLocal[]');

        foreach ($festivos as $festivoLocal) {
            if(!$this->festivoLocalRepository->findOneFecha($festivoLocal->getInicio())) {
                $festivoLocal->setProvincia($provincia);
                $this->festivoLocalRepository->save($festivoLocal);

                //Buscamos los festivos que tengan dias intermedios y no sean acerca de cuatrimestres (inicios y finales de cuatrimestres)
                if($festivoLocal->getInicio() != $festivoLocal->getFinal()) {
                    self::completaFestivosLocalesIntermedios($festivoLocal, $provincia);
                }
            }
        }

        $this->festivoLocalRepository->flush();

        return $festivos;
    }

    public function completaFestivosLocalesIntermedios($festivoLocal, $provincia): void
    {
        $inicio = \DateTime::createFromFormat('d-m-y', $festivoLocal->getInicio());
        $final = \DateTime::createFromFormat('d-m-y', $festivoLocal->getFinal());

        while ($inicio < $final) {
            //Creamos nuestro festivoIntermedio
            $festivoIntermedio = new FestivoLocal();
            $festivoIntermedio->setNombre($festivoLocal->getNombre());
            if(!is_null($festivoLocal->getAbreviatura())) {
                $festivoIntermedio->setAbreviatura($festivoLocal->getAbreviatura());
            }
            $festivoIntermedio->setFinal($festivoLocal->getFinal());
            $festivoIntermedio->setProvincia($provincia);
            //Añadimos un día al inicio
            $inicio->add(new \DateInterval('P1D')); 
            $festivoIntermedio->setInicio($inicio->format('j-n-y'));
            if(!$this->festivoLocalRepository->findOneFechaInicioFinal($festivoIntermedio->getInicio(), $festivoIntermedio->getFinal())) {
                $this->festivoLocalRepository->save($festivoIntermedio);
            }
        }
    }

    public function getProvincias(): array
    {
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosLocales.json');
        $festivosArray = json_decode($festivosJson, true);
        $provinciasArray = array_keys($festivosArray);

        $provinciasFiltrado = [];
        foreach ($provinciasArray as $provincia) {
            preg_match('/festivosLocales(.+)/', $provincia, $coincidencias);
            //Cogemos la segunda de las coincidencias (la primera es la cadena completa)
            $provinciasFiltrado[] = $coincidencias[1];
        }

        return $provinciasFiltrado;
    }

    /**
     * Devuelve los festivos de una localidad/provincia concreta
     */
    public function getFestivosDeProvinciaSeleccionada($nombreProvincia): array
    {
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosLocales.json');
        $festivosArray = json_decode($festivosJson, true);
        $festivosProvincia = $festivosArray["festivosLocales".$nombreProvincia];
        $nombresFestivoCentro = array_column($festivosProvincia, 'nombre');

        return $nombresFestivoCentro;
    }

    /**
     * Devuelve los festivos localidad/provincia
     */
    public function getFestivosLocalesNombres(): array
    {
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosLocales.json');
        $festivosArrayJson = json_decode($festivosJson, true);
        $festivosArray = [];

        // Itera sobre los datos y agrega los nombres de las localidades y festivos al array resultante
        foreach ($festivosArrayJson as $localidad => $festivos) {
            $nombreProvincia = str_replace('festivosLocales', '', $localidad);
            $festivosArray[] = $nombreProvincia . ':';
            foreach ($festivos as $festivo) {
                $festivosArray[] = $festivo['nombre'];
            }
        }

        return $festivosArray;
    }
}
