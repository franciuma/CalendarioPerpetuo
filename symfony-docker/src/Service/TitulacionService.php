<?php

namespace App\Service;

use App\Entity\Titulacion;
use App\Repository\CentroRepository;
use App\Repository\TitulacionRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON titulación y persistirlo en la base de datos.
 */
class TitulacionService
{
    private SerializerInterface $serializer;
    private CentroRepository $centroRepository;
    private TitulacionRepository $titulacionRepository;

    public function __construct(
        SerializerInterface $serializer,
        TitulacionRepository $titulacionRepository,
        CentroRepository $centroRepository
    )
    {
        $this->serializer = $serializer;
        $this->titulacionRepository = $titulacionRepository;
        $this->centroRepository = $centroRepository;
    }

    public function getTitulaciones(): array
    {
        $titulacionesJson = file_get_contents(__DIR__ . '/../resources/titulaciones.json');
        $titulacionesArray = json_decode($titulacionesJson, true);

        //Array de objetos Titulacion
        $titulaciones = [];

        foreach ($titulacionesArray as $titulacion) {
            //Buscamos el centro asociado a la titulación
            $centroDividido = explode("-", $titulacion["centro"]);
            $nombreCentro = $centroDividido[0];
            $provincia = $centroDividido[1];
            $centro = $this->centroRepository->findOneByProvinciaCentro($provincia, $nombreCentro);
            //Denormalizamos la titulación
            $titulacion = $this->serializer->denormalize($titulacion, 'App\Entity\Titulacion');
            $titulacion->setCentro($centro);
            if(!$this->titulacionRepository->findOneBynombreTitulacion($titulacion->getNombreTitulacion())){
                $this->titulacionRepository->save($titulacion);
                $titulaciones[] = $titulacion;
            } else {
                $titulaciones[] = $this->titulacionRepository->findOneBynombreTitulacion($titulacion->getNombreTitulacion());
            }
        }
        $this->titulacionRepository->flush();

        return $titulaciones;
    }

    public function editarTitulacion(Titulacion $titulacionBd) {
        $titulacionesJson = file_get_contents(__DIR__ . '/../resources/titulaciones.json');
        $titulacionesArray = json_decode($titulacionesJson, true);

        $titulacionNueva = $this->serializer->denormalize($titulacionesArray[0], 'App\Entity\Titulacion');

        //Comparamos todas las propiedades del profesor nuevo y original
        if($titulacionBd->getNombreTitulacion() != $titulacionNueva->getNombreTitulacion()) {
            $titulacionBd->setNombreTitulacion($titulacionNueva->getNombreTitulacion());
        }

        if($titulacionBd->getAbreviatura() != $titulacionNueva->getAbreviatura()) {
            $titulacionBd->setAbreviatura($titulacionNueva->getAbreviatura());
        }

        $centroDividido = explode("-", $titulacionesArray[0]["centro"]);
        $nombreCentro = $centroDividido[0];
        $provincia = $centroDividido[1];
        $centroNuevo = $this->centroRepository->findOneByProvinciaCentro($provincia, $nombreCentro);

        //Comparamos los centros
        if($titulacionBd->getCentro()->getNombre() != $nombreCentro ||
            $titulacionBd->getCentro()->getProvincia() != $provincia
        ) {
            $titulacionBd->setCentro($centroNuevo);
        }

        //Guardamos los cambios en la base de datos
        $this->titulacionRepository->save($titulacionBd, true);
    }
}
