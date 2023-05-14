<?php

namespace App\Service;

use App\Entity\Calendario;
use App\Entity\Centro;
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

    public function getCalendario($nombreUsuario, Centro $centro): Calendario
    {
        $calendario = new Calendario();

        //En caso de que venga por profesor
        $profesorSeleccionado = self::getProfesorSeleccionado($nombreUsuario);

        $calendario->setUsuario($profesorSeleccionado);
        $calendario->setCentro($centro);
        $this->calendarioRepository->save($calendario,true);

        return $calendario;
    }

    public function getProfesorSeleccionado($nombreUsuario)
    {
        $nombreCompleto = explode(" ", $nombreUsuario);
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
