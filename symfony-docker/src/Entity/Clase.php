<?php

namespace App\Entity;

use App\Repository\ClaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClaseRepository::class)]
class Clase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $inicio = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $final = null;

    #[ORM\Column(length: 255)]
    private ?string $aula = null;

    #[ORM\Column(length: 255)]
    private ?string $correo = null;

    #[ORM\Column(length: 255)]
    private ?string $asignatura = null;

    #[ORM\Column(length: 255)]
    private ?string $abreviatura = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInicio(): ?\DateTimeInterface
    {
        return $this->inicio;
    }

    public function setInicio(\DateTimeInterface $inicio): self
    {
        $this->inicio = $inicio;

        return $this;
    }

    public function getFinal(): ?\DateTimeInterface
    {
        return $this->final;
    }

    public function setFinal(\DateTimeInterface $final): self
    {
        $this->final = $final;

        return $this;
    }

    public function getAula(): ?string
    {
        return $this->aula;
    }

    public function setAula(string $aula): self
    {
        $this->aula = $aula;

        return $this;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(string $correo): self
    {
        $this->correo = $correo;

        return $this;
    }

    public function getAsignatura(): ?string
    {
        return $this->asignatura;
    }

    public function setAsignatura(string $asignatura): self
    {
        $this->asignatura = $asignatura;

        return $this;
    }

    public function getAbreviatura(): ?string
    {
        return $this->abreviatura;
    }

    public function setAbreviatura(string $abreviatura): self
    {
        $this->abreviatura = $abreviatura;

        return $this;
    }
}
