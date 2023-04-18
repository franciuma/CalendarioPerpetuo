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

    public function __construct(
        ProfesorGrupoRepository $profesorGrupoRepository,
    )
    {
        $this->profesorGrupoRepository = $profesorGrupoRepository;
    }

    public function getProfesorGrupo(Profesor $profesor, array $grupos): void
    {
        foreach ($grupos as $grupo) {
            $profesorGrupo = new ProfesorGrupo();
            $profesorGrupo->setProfesor($profesor);
            $profesorGrupo->setGrupo($grupo);
            $this->profesorGrupoRepository->save($profesorGrupo,true);
        }
    }
}
