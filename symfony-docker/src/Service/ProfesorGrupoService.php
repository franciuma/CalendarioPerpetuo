<?php

namespace App\Service;

use App\Entity\Profesor;
use App\Entity\ProfesorGrupo;
use App\Repository\ProfesorGrupoRepository;

/**
 * Clase utilizada para traducir JSON profesorGrupo y persistir grupos en la base de datos.
 */
class ProfesorGrupoService
{
    private ProfesorGrupoRepository $profesorGrupoRepository;
    private ProfesorGrupo $profesorGrupo;

    public function __construct(
        ProfesorGrupoRepository $profesorGrupoRepository,
        ProfesorGrupo $profesorGrupo
    )
    {
        $this->profesorGrupoRepository = $profesorGrupoRepository;
        $this->profesorGrupo = $profesorGrupo;
    }

    public function getProfesorGrupo(Profesor $profesor, array $grupos): void
    {
        $this->profesorGrupo->setProfesor($profesor);

        foreach ($grupos as $grupo) {
            $this->profesorGrupo->setGrupo($grupo);
        }

        $this->profesorGrupoRepository->save($this->profesorGrupo,true);
    }
}
