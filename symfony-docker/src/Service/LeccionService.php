<?php

namespace App\Service;

use App\Repository\AsignaturaRepository;
use App\Repository\LeccionRepository;
use App\Repository\TitulacionRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON clase y persistirlo en la base de datos.
 */
class LeccionService
{
    private SerializerInterface $serializer;
    private LeccionRepository $leccionRepository;
    private AsignaturaRepository $asignaturaRepository;
    private TitulacionRepository $titulacionRepository;

    public function __construct(
        SerializerInterface $serializer,
        LeccionRepository $leccionRepository,
        AsignaturaRepository $asignaturaRepository,
        TitulacionRepository $titulacionRepository
    )
    {
        $this->serializer = $serializer;
        $this->leccionRepository = $leccionRepository;
        $this->asignaturaRepository = $asignaturaRepository;
        $this->titulacionRepository = $titulacionRepository;
    }

    /**
     * Edita lecciones de una asignatura
     */
    public function editarLecciones($lecciones)
    {
        $asignaturasJson = file_get_contents(__DIR__ . '/../resources/asignaturas.json');
        $asignaturasArray = json_decode($asignaturasJson, true);
        $leccionesArray = $asignaturasArray['asignaturas'][0]['lecciones'];
        $leccionesNuevas = $this->serializer->denormalize($leccionesArray, 'App\Entity\Leccion[]');

        for ($i=0; $i < count($leccionesNuevas); $i++) {

            if($leccionesNuevas[$i]->getTitulo() != $lecciones[$i]->getTitulo()) {
                $lecciones[$i]->setTitulo($leccionesNuevas[$i]->getTitulo());
            }

            if($leccionesNuevas[$i]->getModalidad() != $lecciones[$i]->getModalidad()) {
                $lecciones[$i]->setModalidad($leccionesNuevas[$i]->getModalidad());
            }

            if($leccionesNuevas[$i]->getAbreviatura() != $lecciones[$i]->getAbreviatura()) {
                $lecciones[$i]->setAbreviatura($leccionesNuevas[$i]->getAbreviatura());
            }
        }
    }
}
