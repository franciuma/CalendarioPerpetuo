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
        $calendario = new Calendario();

        //Obtenemos el ultimo profesor introducido en la base de datos
        $ultimoProfesor = $this->profesorRepository->findOneBy([],['id' => 'DESC']);
        if (!$ultimoProfesor) {
            throw new \Exception('No se encontró ningún profesor');
        }
        $calendario->setProfesor($ultimoProfesor);
        $this->calendarioRepository->save($calendario,true);

        return $calendario;
    }
}
