<?php

namespace App\Service;

use App\Entity\Calendario;
use App\Repository\CalendarioRepository;

/**
 * Clase utilizada para crear el calendario y persistirlo en la base de datos.
 */
class CalendarioService
{
    private CalendarioRepository $calendarioRepository;

    public function __construct(
        CalendarioRepository $calendarioRepository
    )
    {
        $this->calendarioRepository = $calendarioRepository;
    }

    public function getCalendario(): Calendario
    {
        $calendario = new Calendario();

        $this->calendarioRepository->save($calendario,true);

        return $calendario;
    }
}
