<?php

namespace App\Service;

use App\Entity\Calendario;
use App\Repository\CalendarioRepository;
use App\Repository\ProfesorRepository;

/**
 * Clase utilizada para crear el calendario y persistirlo en la base de datos.
 */
class CalendarioService
{
    private CalendarioRepository $calendarioRepository;
    private ProfesorRepository $profesorRepository;

    public function __construct(
        CalendarioRepository $calendarioRepository,
        ProfesorRepository $profesorRepository
    )
    {
        $this->calendarioRepository = $calendarioRepository;
        $this->profesorRepository = $profesorRepository;
    }

    public function getCalendario(): Calendario
    {
        $centroJson = file_get_contents(__DIR__ . '/../resources/centro.json');
        $centroArray = json_decode($centroJson, true);

        $calendario = new Calendario();
        //Dividimos el docente en nombre y apellidos
        $nombreProfesor = $centroArray['centro'][0]['profesor'];

        $nombreCompleto = explode(" ", $nombreProfesor);
        //Asignamos el nombre y apellidos
        $nombre = $nombreCompleto[0];
        $apellidoPr = $nombreCompleto[1];
        $apellidoSeg = $nombreCompleto[2];

        $profesorSeleccionado = $this->profesorRepository->findOneByNombreApellidos($nombre, $apellidoPr, $apellidoSeg);
        if(!$profesorSeleccionado) {
            throw new \Exception('No se encontró ningún profesor');
        }

        $calendario->setProfesor($profesorSeleccionado);
        $this->calendarioRepository->save($calendario,true);

        return $calendario;
    }
}
