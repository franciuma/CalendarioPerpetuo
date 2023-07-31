<?php

namespace App\Service;

use App\Repository\FestivoCentroRepository;
use Symfony\Component\Serializer\SerializerInterface;
use App\Entity\Centro;
use App\Entity\FestivoCentro;
use App\Repository\CentroRepository;
use App\Repository\EventoRepository;

/**
 * Clase utilizada para traducir JSON festivoCentro y persistirlo en la base de datos.
 */
class FestivoCentroService
{
    private SerializerInterface $serializer;
    private FestivoCentroRepository $festivoCentroRepository;
    private CentroRepository $centroRepository;
    private EventoRepository $eventoRepository;

    public function __construct(
        SerializerInterface $serializer,
        FestivoCentroRepository $festivoCentroRepository,
        EventoRepository $eventoRepository,
        CentroRepository $centroRepository
    )
    {
        $this->serializer = $serializer;
        $this->festivoCentroRepository = $festivoCentroRepository;
        $this->eventoRepository = $eventoRepository;
        $this->centroRepository = $centroRepository;
    }

    public function getFestivosCentro(Centro $centro, $curso): array
    {
        $anio = substr($curso[0], 2, 3);
        $anioSiguiente = substr($curso[1], 2, 3);
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
            if(!$this->festivoCentroRepository->findOneFechaCentro($festivoCentro->getInicio(), $nombreCentro)) {
                $festivoCentro->setCentro($centro);
                $this->festivoCentroRepository->save($festivoCentro);

                //Buscamos los festivos que tengan dias intermedios y no sean acerca de cuatrimestres (inicios y finales de cuatrimestres)
                if($festivoCentro->getInicio() != $festivoCentro->getFinal() && !strstr($festivoCentro->getNombre(), 'cuatrimestre')) {
                    self::completaFestivosCentroIntermedios($festivoCentro);
                }
            }
        }

        $this->festivoCentroRepository->flush();

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
            if(!is_null($festivoCentro->getAbreviatura())) {
                $festivoIntermedio->setAbreviatura($festivoCentro->getAbreviatura());
            }
            $festivoIntermedio->setFinal($festivoCentro->getFinal());
            $festivoIntermedio->setCentro($festivoCentro->getCentro());
            //Añadimos un día al inicio
            $inicio->add(new \DateInterval('P1D')); 
            $festivoIntermedio->setInicio($inicio->format('j-n-y'));
            if(!$this->festivoCentroRepository->findOneFechaInicioFinal($festivoIntermedio->getInicio(), $festivoIntermedio->getFinal())) {
                $this->festivoCentroRepository->save($festivoIntermedio);
            }
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

    /**
     * Devuelve los nombres de los centros del json festivosCentro.json
     */
    public function getNombreCentroProvincia(): array
    {
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosCentro.json');
        $festivosArray = json_decode($festivosJson, true);
        $centrosArray = array_keys($festivosArray);

        $centroFiltrado = [];
        foreach ($centrosArray as $centroNombre) {
            preg_match('/festivosCentro(.+)-(.+)/', $centroNombre, $coincidencias);
            //Cogemos la segunda de las coincidencias (la primera es la cadena completa)
            $centroFiltrado[] = $coincidencias[1]."-".$coincidencias[2];
        }

        return $centroFiltrado;
    }

    /**
     * Devuelve los festivos de un centro concreto
     */
    public function getFestivosDeCentroSeleccionado($nombreCentro): array
    {
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosCentro.json');
        $festivosArray = json_decode($festivosJson, true);
        $festivosCentro = $festivosArray["festivosCentro".$nombreCentro];
        $nombresFestivoCentro = array_column($festivosCentro, 'nombre');

        return $nombresFestivoCentro;
    }

    /**
     * Devuelve los festivos de los centros
     */
    public function getFestivosCentroNombres(): array
    {
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosCentro.json');
        $festivosArrayJson = json_decode($festivosJson, true);
        $festivosArray = [];

        // Itera sobre los datos y agrega los nombres de los centros y festivos al array resultante
        foreach ($festivosArrayJson as $centro => $festivos) {
            $nombreCentro = str_replace('festivosCentro', '', $centro);
            $nombreCentro = str_replace('-', ' ', $nombreCentro);
            $festivosArray[] = $nombreCentro . ':';
            foreach ($festivos as $festivo) {
                $festivosArray[] = $festivo['nombre'];
            }
        }

        return $festivosArray;
    }

    /**
     * Devuelve los festivos de un año concreto
     */
    public function filtrarFestivos($anios, $centroId): array
    {
        $anioAnterior = (int)$anios[0];
        $anioActual = (int)$anios[1];

        $festivosCentro = $this->festivoCentroRepository->findByCentroId($centroId);

        $festivosFiltrados = [];
        foreach ($festivosCentro as $festivoCentro) {
            $inicio = \DateTime::createFromFormat('d-m-y', $festivoCentro->getInicio());
            //La fecha debe estar entre septiembre de anio[0] y julio de anio[1]
            $anioInicio = (int)$inicio->format('Y');
            $mesInicio = (int)$inicio->format('m');
            if(($mesInicio >= 9 && $anioInicio == $anioAnterior) || ($mesInicio <= 6 && $anioInicio == $anioActual)) {
                $festivosFiltrados[] = $festivoCentro;
            }
        }
        return $festivosFiltrados;
    }

    /**
     * Busca los festivos a partir de un nombre
     */
    public function buscarPorNombre($festivos, $nombre, $centro): FestivoCentro
    {
        $festivoCentro = null;
        foreach ($festivos as $festivo) {
            if($festivo->getNombre() == $nombre &&
                $festivo->getCentro()->getNombre()."-".$festivo->getCentro()->getProvincia() == $centro
            ) {
                $festivoCentro = $festivo;
                return $festivoCentro;
            }
        }

        return $festivoCentro;
    }

    public function editaFestivoCentro($curso, $festivoCentro, $festivoCentroNuevo): void
    {
        $anio = substr($curso[0], 2, 3);
        $anioSiguiente = substr($curso[1], 2, 3);

        $festivoCentroNuevo[0]['inicio'] = str_replace('%AN%', $anio, $festivoCentroNuevo[0]['inicio']);
        $festivoCentroNuevo[0]['inicio'] = str_replace('%AC%', $anioSiguiente, $festivoCentroNuevo[0]['inicio']);
        $festivoCentroNuevo[0]['final'] = str_replace('%AN%', $anio, $festivoCentroNuevo[0]['final']);
        $festivoCentroNuevo[0]['final'] = str_replace('%AC%', $anioSiguiente, $festivoCentroNuevo[0]['final']);

        if($festivoCentroNuevo[0]['inicio'] == $festivoCentroNuevo[0]['final']) {
            $festivoCentro->setInicio($festivoCentroNuevo[0]['inicio']);
            $festivoCentro->setFinal($festivoCentroNuevo[0]['final']);
        } else {
            $nombreFestivoLocal = $festivoCentroNuevo[0]['nombre'];
            //Obtenemos los ids de los festivos locales
            $ids = $this->festivoCentroRepository->obtenerids($nombreFestivoLocal);
            //Borramos los eventos asociados
            foreach ($ids as $id) {
                $this->eventoRepository->removeByFestivoLocalId($id);
            }
            //Borramos los intermedios
            $this->festivoCentroRepository->removeByNombreCentro($nombreFestivoLocal, $festivoCentro->getCentro()->getId());

            self::completaFestivosCentroIntermedios($festivoCentro);
        }
    }

    public function eliminarFestivoCompleto($nombreFestivo, $centro): void
    {
        $festivosJson = file_get_contents(__DIR__ . '/../resources/festivosCentro.json');
        $festivosArray = json_decode($festivosJson, true);

        $festivosCentro = &$festivosArray['festivosCentro'.$centro];
        foreach ($festivosCentro as $indice => $festivo) {
            if ($festivo['nombre'] === $nombreFestivo) {
                unset($festivosCentro[$indice]);
                break;
            }
        }

        $festivosCentro = array_values($festivosCentro);

        // Codificar el array actualizado a JSON
        $jsonActualizado = json_encode($festivosArray, JSON_PRETTY_PRINT);

        // Guardar el JSON actualizado nuevamente en el archivo
        file_put_contents("/app/src/Resources/festivosCentro.json", $jsonActualizado);

        //Obtenemos los ids de los festivos de centro
        $ids = $this->festivoCentroRepository->obtenerids($nombreFestivo);
        //Borramos los eventos asociados
        foreach ($ids as $id) {
            $this->eventoRepository->removeByFestivoLocalId($id);
        }
        //Borramos los festivos locales
        $centroFormato = explode("-",$centro);
        $centroObjeto = $this->centroRepository->findOneByNombre($centroFormato[0]);
        $this->festivoCentroRepository->removeByNombreCentro($nombreFestivo, $centroObjeto->getId());
    }
}
