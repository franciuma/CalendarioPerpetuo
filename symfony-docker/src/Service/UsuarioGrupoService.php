<?php

namespace App\Service;

use App\Entity\Usuario;
use App\Entity\UsuarioGrupo;
use App\Repository\UsuarioGrupoRepository;

/**
 * Clase utilizada para traducir JSON UsuarioGrupo y persistir grupos en la base de datos.
 */
class UsuarioGrupoService
{
    private UsuarioGrupoRepository $usuarioGrupoRepository;

    public function __construct(
        UsuarioGrupoRepository $usuarioGrupoRepository,
    )
    {
        $this->usuarioGrupoRepository = $usuarioGrupoRepository;
    }

    public function getUsuarioGrupo(Usuario $usuario, array $grupos): void
    {
        foreach ($grupos as $grupo) {
            $usuarioGrupo = new UsuarioGrupo();
            $usuarioGrupo->setUsuario($usuario);
            $usuarioGrupo->setGrupo($grupo);
            $this->usuarioGrupoRepository->save($usuarioGrupo,true);
        }
    }
}
