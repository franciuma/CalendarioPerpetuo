<?php

namespace App\Service;

use App\Entity\Usuario;
use App\Entity\UsuarioGrupo;
use App\Repository\GrupoRepository;
use App\Repository\UsuarioGrupoRepository;

/**
 * Clase utilizada para traducir JSON UsuarioGrupo y persistir grupos en la base de datos.
 */
class UsuarioGrupoService
{
    private UsuarioGrupoRepository $usuarioGrupoRepository;
    private GrupoRepository $grupoRepository;

    public function __construct(
        UsuarioGrupoRepository $usuarioGrupoRepository,
        GrupoRepository $grupoRepository
    )
    {
        $this->usuarioGrupoRepository = $usuarioGrupoRepository;
        $this->grupoRepository = $grupoRepository;
    }

    public function getUsuarioGrupo(Usuario $usuario, array $grupos): void
    {
        foreach ($grupos as $grupo) {
            if(is_null($this->usuarioGrupoRepository->findOneByUsuarioGrupo($usuario->getId(), $grupo->getId()))) {
                $grupoObjeto = $this->grupoRepository->findOneById($grupo->getId());
                $usuarioGrupo = new UsuarioGrupo();
                $usuarioGrupo->setUsuario($usuario);
                $usuarioGrupo->setGrupo($grupoObjeto);
                $grupoObjeto->addUsuarioGrupo($usuarioGrupo);
                $this->usuarioGrupoRepository->save($usuarioGrupo);
            }
        }
        $this->usuarioGrupoRepository->flush();
    }
}
