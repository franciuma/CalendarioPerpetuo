<?php

namespace App\Service;

use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\UsuarioRepository;

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

    public function getProfesor()
    {
        $profesorJson = file_get_contents(__DIR__ . '/../resources/profesorGrupo.json');
        $profesorArray = json_decode($profesorJson, true);

        $profesor = $this->serializer->denormalize($profesorArray['profesor'][0], 'App\Entity\Usuario');

        $this->usuarioRepository->save($profesor,true);

        return $profesor;
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
