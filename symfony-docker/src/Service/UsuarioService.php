<?php

namespace App\Service;

use App\Entity\Usuario;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\UsuarioRepository;
use Exception;

/**
 * Clase utilizada para traducir JSON usuarioGrupo y persistir un usuario en la base de datos.
 */
class UsuarioService
{
    private SerializerInterface $serializer;
    private UsuarioRepository $usuarioRepository;

    public function __construct(
        SerializerInterface $serializer,
        UsuarioRepository $usuarioRepository
    )
    {
        $this->serializer = $serializer;
        $this->usuarioRepository = $usuarioRepository;
    }

    public function getUsuario()
    {
        $usuarioJson = file_get_contents(__DIR__ . '/../resources/usuarioGrupo.json');
        $usuarioArray = json_decode($usuarioJson, true);

        if(isset($usuarioArray['profesor'][0])) {
            $usuario = $this->serializer->denormalize($usuarioArray['profesor'][0], 'App\Entity\Usuario');
        } else {
            $usuario = $this->serializer->denormalize($usuarioArray['alumno'][0], 'App\Entity\Usuario');
        }

        if(is_null($usuario)) {
            throw new Exception("Usuario nulo");
        }

        $this->usuarioRepository->save($usuario,true);

        return $usuario;
    }

    /**
     * Edita un profesor, comparando el que recibe con el de la base de datos.
     */
    public function editarProfesor(Usuario $profesor)
    {
        $profesorJson = file_get_contents(__DIR__ . '/../resources/usuarioGrupo.json');
        $profesorArray = json_decode($profesorJson, true);

        $profesorNuevo = $this->serializer->denormalize($profesorArray['profesor'][0], 'App\Entity\Usuario');

        //Comparamos todas las propiedades del profesor nuevo y original
        if($profesor->getNombre() != $profesorNuevo->getNombre()) {
            $profesor->setNombre($profesorNuevo->getNombre());
        }

        if($profesor->getPrimerApellido() != $profesorNuevo->getPrimerApellido()) {
            $profesor->setPrimerApellido($profesorNuevo->getPrimerApellido());
        }

        if($profesor->getSegundoApellido() != $profesorNuevo->getSegundoApellido()) {
            $profesor->setSegundoApellido($profesorNuevo->getSegundoApellido());
        }

        if($profesor->getCorreo() != $profesorNuevo->getCorreo()) {
            $profesor->setCorreo($profesorNuevo->getCorreo());
        }

        if($profesor->getDespacho() != $profesorNuevo->getDespacho()) {
            $profesor->setDespacho($profesorNuevo->getDespacho());
        }

        //Guardamos los cambios en la base de datos
        $this->usuarioRepository->save($profesor, true);
    }

    public function getAllProfesoresNombreCompleto($conCalendario): array
    {
        if($conCalendario) {
            $profesores = $this->usuarioRepository->findAllProfesoresConCalendario();
        } else {
            $profesores = $this->usuarioRepository->findAllProfesores();
        }

        $nombreProfesores = array_map(function($profesor) {
            $nombre = $profesor->getNombre();
            $apellidop = $profesor->getPrimerApellido();
            $apellidos = $profesor->getSegundoApellido();
            return $nombre." ".$apellidop." ".$apellidos;
        }, $profesores);

        return $nombreProfesores;
    }
}
