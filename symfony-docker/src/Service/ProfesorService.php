<?php

namespace App\Service;

use App\Repository\ProfesorRepository;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Clase utilizada para traducir JSON profesorGrupo y persistir profesor en la base de datos.
 */
class ProfesorService
{
    private SerializerInterface $serializer;
    private ProfesorRepository $profesorRepository;

    public function __construct(
        SerializerInterface $serializer,
        ProfesorRepository $profesorRepository
    )
    {
        $this->serializer = $serializer;
        $this->profesorRepository = $profesorRepository;
    }

    public function getProfesor(): void
    {
        $profesorJson = file_get_contents(__DIR__ . '/../resources/profesorGrupo.json');
        $profesorArray = json_decode($profesorJson, true);

        $profesor = $this->serializer->denormalize($profesorArray['profesor'][0], 'App\Entity\Profesor');

        $this->profesorRepository->save($profesor,true);
    }
}
