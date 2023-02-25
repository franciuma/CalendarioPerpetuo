<?php

namespace App\Entity;

use App\Repository\FestivoNacionalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FestivoNacionalRepository::class)]
class FestivoNacional
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'festivosNacionales')]
    private ?Calendario $calendario = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $inicio = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $final = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCalendario(): ?Calendario
    {
        return $this->calendario;
    }

    public function setCalendario(?Calendario $calendario): self
    {
        $this->calendario = $calendario;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
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
}
