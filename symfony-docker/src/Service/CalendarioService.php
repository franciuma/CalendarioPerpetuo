<?php

namespace App\Service;

use App\Entity\Calendario;
use App\Repository\CalendarioRepository;
use App\Repository\UsuarioRepository;

/**
 * Clase utilizada para crear el calendario y persistirlo en la base de datos.
 */
class CalendarioService
{
    private CalendarioRepository $calendarioRepository;
    private UsuarioRepository $usuarioRepository;

    public function __construct(
        CalendarioRepository $calendarioRepository,
        UsuarioRepository $usuarioRepository,
    )
    {
        $this->calendarioRepository = $calendarioRepository;
        $this->usuarioRepository = $usuarioRepository;
    }

    public function getCalendario(): Calendario
    {
        $centroJson = file_get_contents(__DIR__ . '/../resources/centro.json');
        $centroArray = json_decode($centroJson, true);

        $calendario = new Calendario();
        
        $profesorSeleccionado = self::getProfesorSeleccionado($centroArray);

        $calendario->setUsuario($profesorSeleccionado);
        $this->calendarioRepository->save($calendario,true);

        return $calendario;
    }

    public function getProfesorSeleccionado($centroArray)
    {
        //Dividimos el docente en nombre y apellidos
        $nombreProfesor = $centroArray['centro'][0]['profesor'];

        $nombreCompleto = explode(" ", $nombreProfesor);
        //Asignamos el nombre y apellidos
        $nombre = $nombreCompleto[0];
        $apellidoPr = $nombreCompleto[1];
        $apellidoSeg = $nombreCompleto[2];

        //Obtenemos el usuario
        $profesorSeleccionado = $this->usuarioRepository->findOneByNombreApellidos($nombre, $apellidoPr, $apellidoSeg, 'Profesor');

        if(!$profesorSeleccionado) {
            throw new \Exception('No se encontró ningún profesor');
        }

        return $profesorSeleccionado;
    }
}
