<?php

namespace App\Service;

use App\Entity\Grupo;
use App\Repository\GrupoRepository;
use App\Repository\AsignaturaRepository;
use App\Repository\ClaseRepository;
use App\Repository\EventoRepository;
use App\Repository\UsuarioGrupoRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON usuarioGrupo y persistir grupos en la base de datos.
 */
class GrupoService
{
    private SerializerInterface $serializer;
    private GrupoRepository $grupoRepository;
    private AsignaturaRepository $asignaturaRepository;
    private ClaseRepository $claseRepository;
    private UsuarioGrupoRepository $usuarioGrupoRepository;
    private EventoRepository $eventoRepository;

    public function __construct(
        SerializerInterface $serializer,
        GrupoRepository $grupoRepository,
        AsignaturaRepository $asignaturaRepository,
        ClaseRepository $claseRepository,
        UsuarioGrupoRepository $usuarioGrupoRepository,
        EventoRepository $eventoRepository
    )
    {
        $this->serializer = $serializer;
        $this->grupoRepository = $grupoRepository;
        $this->asignaturaRepository = $asignaturaRepository;
        $this->claseRepository = $claseRepository;
        $this->usuarioGrupoRepository = $usuarioGrupoRepository;
        $this->eventoRepository = $eventoRepository;
    }

    public function getGrupos($persistirBd)
    {
        $grupoJSON = file_get_contents(__DIR__ . '/../resources/usuarioGrupo.json');
        $gruposArray = json_decode($grupoJSON, true);

        $gruposDatos = $gruposArray['grupos'];
        $grupos = [];

        foreach ($gruposDatos as $grupoDatos) {
            $asignaturaNombre = $grupoDatos['asignaturaNombre'];
            $grupo = $this->serializer->denormalize($grupoDatos, 'App\Entity\Grupo');
            $asignatura = $this->asignaturaRepository->findOneByNombre($asignaturaNombre);
            $grupo->setAsignatura($asignatura);
            if($persistirBd) {
                $this->grupoRepository->save($grupo);
            }
            $grupos[] = $grupo;
        }

        $this->grupoRepository->flush();

        return $grupos;
    }

    public function editarGrupos(array $grupos)
    {
        $gruposNuevos = self::getGrupos(false);
        $gruposActualizados = [];

        //Recorremos los grupos antiguos, y comparamos con los nuevos.
        //Si no existe ningún grupo antiguo en los nuevos, se borra.
        foreach ($grupos as $grupo)
        {
            $grupoExistente = $this->buscarGrupo($grupo, $gruposNuevos, "borrar");
            if(!$grupoExistente) {
                //Si el grupo no está en los nuevos, es porque se ha borrado
                //Además se borrara sus clases asociadas, usuario_grupo y eventos
                $usuarioGrupos = $this->usuarioGrupoRepository->findByGrupoId($grupo->getId());
                $this->usuarioGrupoRepository->removeUsuarioGrupos($usuarioGrupos);
                $clases = $this->claseRepository->findByGrupoId($grupo->getId());
                foreach ($clases as $clase) {
                    $evento = $this->eventoRepository->findByClaseId($clase->getId());
                    $this->eventoRepository->remove($evento);
                }
                $this->claseRepository->removeClases($clases);
                $this->grupoRepository->remove($grupo);
            }
        }

        foreach ($gruposNuevos as $grupoNuevo) {
            // Buscamos el grupo en la bd
            $grupoAntiguo = $this->buscarGrupo($grupoNuevo, $grupos, "editar");

            //Si se encuentra, se modifica el grupo
            if($grupoAntiguo) {
                if($grupoAntiguo->getDiasTeoria() != $grupoNuevo->getDiasTeoria()){
                    $grupoAntiguo->setDiasTeoria($grupoNuevo->getDiasTeoria());
                }

                if($grupoAntiguo->getDiasPractica() != $grupoNuevo->getDiasPractica()){
                    $grupoAntiguo->setDiasPractica($grupoNuevo->getDiasPractica());
                }
                //Actualizamos la base de datos
                $this->grupoRepository->save($grupoAntiguo);
            } else {
                //Si no corresponde a ningún grupo, hay que añadirlo.
                $this->grupoRepository->save($grupoNuevo);
                $gruposActualizados[] = $grupoNuevo;
            }
        }

        $this->grupoRepository->flush();

        return $gruposActualizados;
    }

    /**
     * Busca un Grupo en un array Grupos.
     */
    public function buscarGrupo(Grupo $grupo, $gruposArray, $accion)
    {
        foreach ($gruposArray as $grupoObjeto) {
            if($grupoObjeto->getAsignatura()->getNombre() == $grupo->getAsignatura()->getNombre() &&
                $grupoObjeto->getLetra() == $grupo->getLetra() &&
                $grupoObjeto->getHorario() == $grupo->getHorario()
            ) {
                if($accion == "borrar") {
                    $grupoEditado = $this->grupoRepository->findOneById($grupo->getId());
                } else {
                    $grupoEditado = $this->grupoRepository->findOneById($grupoObjeto->getId());
                }
                
                return $grupoEditado;
            } 
        }
    }

    /**
     * Busca del Json los grupos del usuario y los localiza en la base de datos para devolverlos en un array.
     */
    public function buscarGruposJson(): array
    {
        $grupoJSON = file_get_contents(__DIR__ . '/../resources/usuarioGrupo.json');
        $gruposArray = json_decode($grupoJSON, true);

        $gruposDatos = $gruposArray['grupos'];
        $grupos = [];

        foreach ($gruposDatos as $grupoDatos) {
            $asignaturaNombre = $grupoDatos['asignaturaNombre'];
            $asignaturaLetra = $grupoDatos['letra'];
            $horario = $grupoDatos['horario'];
            $asignatura = $this->asignaturaRepository->findOneByNombre($asignaturaNombre);
            $grupo = $this->grupoRepository->findByAsigLetraHorario($asignatura->getId(), $asignaturaLetra, $horario);
            $grupos[] = $grupo;
        }

        $this->grupoRepository->flush();

        return $grupos;
    }
}
