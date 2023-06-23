<?php

namespace App\Entity;

use App\Repository\LeccionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LeccionRepository::class)]
class Leccion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titulo = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private ?Asignatura $asignatura = null;

    #[ORM\Column(length: 255)]
    private ?string $modalidad = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $abreviatura = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getAsignatura(): ?Asignatura
    {
        return $this->asignatura;
    }

    public function setAsignatura(?Asignatura $asignatura): self
    {
        $this->asignatura = $asignatura;

        return $this;
    }

    public function getModalidad(): ?string
    {
        return $this->modalidad;
    }

    public function setModalidad(string $modalidad): self
    {
        $this->modalidad = $modalidad;

        return $this;
    }

    public function getAbreviatura(): ?string
    {
        return $this->abreviatura;
    }

    public function setAbreviatura(?string $abreviatura): static
    {
        $this->abreviatura = $abreviatura;

        return $this;
    }
}
