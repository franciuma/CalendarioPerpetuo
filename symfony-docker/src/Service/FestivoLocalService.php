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

    public function __construct(
        SerializerInterface $serializer,
        FestivoLocalRepository $festivoLocalRepository,
    )
    {
        $this->serializer = $serializer;
        $this->festivoLocalRepository = $festivoLocalRepository;
    }

    public function getFestivosLocales(Centro $centro): array
    {
        $anio = substr(CalendarioController::ANIO, 2, 3);
        $anioSiguiente = substr(CalendarioController::ANIO_SIGUIENTE, 2, 3);
        $provincia = $centro->getProvincia();

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
                $this->festivoLocalRepository->save($festivoLocal,true);

                //Buscamos los festivos que tengan dias intermedios y no sean acerca de cuatrimestres (inicios y finales de cuatrimestres)
                if($festivoLocal->getInicio() != $festivoLocal->getFinal()) {
                    self::completaFestivosCentroIntermedios($festivoLocal, $provincia);
                }
            }
        }

        return $festivos;
    }

    public function completaFestivosCentroIntermedios($festivoLocal, $provincia): void
    {
        $inicio = \DateTime::createFromFormat('d-m-y', $festivoLocal->getInicio());
        $final = \DateTime::createFromFormat('d-m-y', $festivoLocal->getFinal());

        while ($inicio < $final) {
            //Creamos nuestro festivoIntermedio
            $festivoIntermedio = new FestivoLocal();
            $festivoIntermedio->setNombre($festivoLocal->getNombre());
            $festivoIntermedio->setAbreviatura($festivoLocal->getAbreviatura());
            $festivoIntermedio->setFinal($festivoLocal->getFinal());
            $festivoIntermedio->setProvincia($provincia);
            //Añadimos un día al inicio
            $inicio->add(new \DateInterval('P1D')); 
            $festivoIntermedio->setInicio($inicio->format('j-n-y'));
            $this->festivoLocalRepository->save($festivoIntermedio,true);
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
}
