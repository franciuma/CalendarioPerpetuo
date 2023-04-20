<?php

namespace App\Entity;

use App\Repository\GrupoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GrupoRepository::class)]
class Grupo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $letra = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Asignatura $asignatura = null;

    #[ORM\Column(length: 255)]
    private ?string $horario = null;

    #[ORM\Column(type: Types::JSON)]
    private array $dias_teoria = [];

    #[ORM\Column(type: Types::JSON)]
    private array $dias_practica = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLetra(): ?string
    {
        return $this->letra;
    }

    public function setLetra(string $letra): self
    {
        $this->letra = $letra;

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

    public function getHorario(): ?string
    {
        return $this->horario;
    }

    public function setHorario(string $horario): self
    {
        $this->horario = $horario;

        return $this;
    }

    public function getDiasTeoria(): array
    {
        return $this->dias_teoria;
    }

    public function setDiasTeoria(array $dias_teoria): self
    {
        $this->dias_teoria = $dias_teoria;

        return $this;
    }

    public function getDiasPractica(): array
    {
        return $this->dias_practica;
    }

    public function setDiasPractica(array $dias_practica): self
    {
        $this->dias_practica = $dias_practica;

        return $this;
    }
}
