<?php

namespace App\Service;

use App\Repository\FestivoCentroRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\CalendarioController;
use App\Entity\Centro;
use App\Entity\FestivoCentro;

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
        $provincia = $centro->getProvincia();

        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosCentro.json');
        $festivosArray = json_decode($festivosJson, true);

        foreach ($festivosArray['festivosCentro'.$nombreCentro.'-'.$provincia] as &$festivo) {
            $festivo['inicio'] = str_replace('%AN%', $anio, $festivo['inicio']);
            $festivo['inicio'] = str_replace('%AC%', $anioSiguiente, $festivo['inicio']);
            $festivo['final'] = str_replace('%AN%', $anio, $festivo['final']);
            $festivo['final'] = str_replace('%AC%', $anioSiguiente, $festivo['final']);
        }

        $festivos = $this->serializer->denormalize($festivosArray['festivosCentro'.$nombreCentro.'-'.$provincia], 'App\Entity\FestivoCentro[]');

        foreach ($festivos as $festivoCentro) {
            if(!$this->festivoCentroRepository->findOneFecha($festivoCentro->getInicio())) {
                $festivoCentro->setCentro($centro);
                $this->festivoCentroRepository->save($festivoCentro,true);
            }
            //Buscamos los festivos que tengan dias intermedios y no sean acerca de cuatrimestres (inicios y finales de cuatrimestres)
            if($festivoCentro->getInicio() != $festivoCentro->getFinal() && !strstr($festivoCentro->getNombre(), 'cuatrimestre')) {
                self::completaFestivosCentroIntermedios($festivoCentro);
            }
        }

        return $festivos;
    }

    public function completaFestivosCentroIntermedios($festivoCentro): void
    {
        $inicio = \DateTime::createFromFormat('d-m-y', $festivoCentro->getInicio());
        $final = \DateTime::createFromFormat('d-m-y', $festivoCentro->getFinal());

        while ($inicio < $final) {
            //Creamos nuestro festivoIntermedio
            $festivoIntermedio = new FestivoCentro();
            $festivoIntermedio->setNombre($festivoCentro->getNombre());
            $festivoIntermedio->setAbreviatura($festivoCentro->getAbreviatura());
            $festivoIntermedio->setFinal($festivoCentro->getFinal());
            $festivoIntermedio->setCentro($festivoCentro->getCentro());
            //Añadimos un día al inicio
            $inicio->add(new \DateInterval('P1D')); 
            $festivoIntermedio->setInicio($inicio->format('j-n-y'));
            $this->festivoCentroRepository->save($festivoIntermedio,true);
        }
    }

    public function getNombreCentros(): array
    {
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosCentro.json');
        $festivosArray = json_decode($festivosJson, true);
        $centrosArray = array_keys($festivosArray);

        $centroFiltrado = [];
        foreach ($centrosArray as $centroNombre) {
            preg_match('/festivosCentro(.+)-(.+)/', $centroNombre, $coincidencias);
            //Cogemos la segunda de las coincidencias (la primera es la cadena completa)
            $centroFiltrado[] = $coincidencias[1];
        }

        return $centroFiltrado;
    }
}
