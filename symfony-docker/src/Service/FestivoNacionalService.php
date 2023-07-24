<?php

namespace App\Service;

use App\Repository\FestivoNacionalRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Controller\CalendarioController;
use App\Entity\FestivoNacional;
use App\Repository\EventoRepository;

/**
 * Clase utilizada para traducir JSON festivoNacional y persistirlo en la base de datos.
 */
class FestivoNacionalService
{
    private SerializerInterface $serializer;
    private FestivoNacionalRepository $festivoNacionalRepository;
    private EventoRepository $eventoRepository;

    public function __construct(
        SerializerInterface $serializer,
        FestivoNacionalRepository $festivoNacionalRepository,
        EventoRepository $eventoRepository
    )
    {
        $this->serializer = $serializer;
        $this->festivoNacionalRepository = $festivoNacionalRepository;
        $this->eventoRepository = $eventoRepository;
    }

    public function getFestivosNacionales($curso, $insertaBd): array
    {
        $anio = substr($curso[0], 2, 3);
        $anioSiguiente = substr($curso[1], 2, 3);

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

        if($insertaBd) {
            $festivos = $this->serializer->denormalize($festivosArray['festivosNacionales-España'], 'App\Entity\FestivoNacional[]');

            foreach ($festivos as $festivoNacional) {
                if(!$this->festivoNacionalRepository->findOneFecha($festivoNacional->getInicio())) {
                    $this->festivoNacionalRepository->save($festivoNacional);
                }
                //Buscamos los festivos que tengan dias intermedios y no sean acerca de cuatrimestres (inicios y finales de cuatrimestres)
                if($festivoNacional->getInicio() != $festivoNacional->getFinal()) {
                    self::completaFestivosNacionalIntermedios($festivoNacional);
                }
            }

            $this->festivoNacionalRepository->flush();
        }

        return $arrayNombreFestivos;
    }

    public function editaFestivoNacional($curso, $festivoNacional, $festivoNacionalNuevo): void
    {
        $anio = substr($curso[0], 2, 3);
        $anioSiguiente = substr($curso[1], 2, 3);

        $festivoNacionalNuevo[0]['inicio'] = str_replace('%AN%', $anio, $festivoNacionalNuevo[0]['inicio']);
        $festivoNacionalNuevo[0]['inicio'] = str_replace('%AC%', $anioSiguiente, $festivoNacionalNuevo[0]['inicio']);
        $festivoNacionalNuevo[0]['final'] = str_replace('%AN%', $anio, $festivoNacionalNuevo[0]['final']);
        $festivoNacionalNuevo[0]['final'] = str_replace('%AC%', $anioSiguiente, $festivoNacionalNuevo[0]['final']);

        if($festivoNacionalNuevo[0]['inicio'] == $festivoNacionalNuevo[0]['final']) {
            $festivoNacional->setInicio($festivoNacionalNuevo[0]['inicio']);
            $festivoNacional->setFinal($festivoNacionalNuevo[0]['final']);
        } else {
            $nombreFestivoNacional = $festivoNacionalNuevo[0]['nombre'];
            //Obtenemos los ids de los festivosNacionales
            $ids = $this->festivoNacionalRepository->obtenerids($nombreFestivoNacional);
            //Borramos los eventos asociados
            foreach ($ids as $id) {
                $this->eventoRepository->removeByFestivoNacionalId($id);
            }
            //Borramos los intermedios
            $this->festivoNacionalRepository->removeByNombre($nombreFestivoNacional);
            self::completaFestivosNacionalIntermedios($festivoNacional);
        }
    }

    public function completaFestivosNacionalIntermedios($festivoNacional): void
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
            if(!$this->festivoNacionalRepository->findOneFechaInicioFinal($festivoIntermedio->getInicio(), $festivoIntermedio->getFinal())) {
                $this->festivoNacionalRepository->save($festivoIntermedio);
            }
        }
    }

    /**
     * Devuelve los festivos nacionales de España
     */
    public function getFestivosNacionalesNombres(): array
    {
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosNacionales.json');
        $festivosArrayJson = json_decode($festivosJson, true);
        $festivosArray = [];

        $festivosNacionales = $festivosArrayJson['festivosNacionales-España'];
        foreach ($festivosNacionales as $festivo) {
            $festivosArray[] = $festivo['nombre'];
        }

        return $festivosArray;
    }

    /**
     * Devuelve los festivos de un año concreto
     */
    public function filtrarFestivos($anios): array
    {
        $anioAnterior = (int)$anios[0];
        $anioActual = (int)$anios[1];

        $festivosNacionales = $this->festivoNacionalRepository->findAll();

        foreach ($festivosNacionales as $festivoNacional) {
            $inicio = \DateTime::createFromFormat('d-m-y', $festivoNacional->getInicio());
            //La fecha debe estar entre septiembre de anio[0] y julio de anio[1]
            $anioInicio = (int)$inicio->format('Y');
            $mesInicio = (int)$inicio->format('m');
            if(($mesInicio >= 9 && $anioInicio == $anioAnterior) || ($mesInicio <= 6 && $anioInicio == $anioActual)) {
                $festivosFiltrados[] = $festivoNacional;
            }
        }
        return $festivosFiltrados;
    }

    /**
     * Busca los festivos a partir de un nombre
     */
    public function buscarPorNombre($festivos, $nombre): FestivoNacional
    {
        $festivoNacional = null;
        foreach ($festivos as $festivo) {
            if($festivo->getNombre() == $nombre) {
                $festivoNacional = $festivo;
                return $festivoNacional;
            }
        }

        return $festivoNacional;
    }
}
