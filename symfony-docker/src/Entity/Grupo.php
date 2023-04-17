<?php

namespace App\Entity;

use App\Repository\GrupoRepository;
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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dias_teoria = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dias_practica = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Asignatura $asignatura = null;

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

    public function getDiasTeoria(): ?string
    {
        return $this->dias_teoria;
    }

    public function setDiasTeoria(?string $dias_teoria): self
    {
        $this->dias_teoria = $dias_teoria;

        return $this;
    }

    public function getDiasPractica(): ?string
    {
        return $this->dias_practica;
    }

    public function setDiasPractica(?string $dias_practica): self
    {
        $this->dias_practica = $dias_practica;

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
}
