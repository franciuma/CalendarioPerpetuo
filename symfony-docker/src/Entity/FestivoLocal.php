<?php

namespace App\Entity;

use App\Interface\FestivoInterface;
use App\Repository\FestivoLocalRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FestivoLocalRepository::class)]
class FestivoLocal implements FestivoInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'festivosLocales')]
    private ?Calendario $calendario = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $abreviatura = null;

    #[ORM\Column(length: 255)]
    private ?string $inicio = null;

    #[ORM\Column(length: 255)]
    private ?string $final = null;

    #[ORM\Column(length: 255)]
    private ?string $provincia = null;

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

    public function getAbreviatura(): ?string
    {
        return $this->abreviatura;
    }

    public function setAbreviatura(string $abreviatura): self
    {
        $this->abreviatura = $abreviatura;

        return $this;
    }

    public function getInicio(): ?string
    {
        return $this->inicio;
    }

    public function setInicio(string $inicio): self
    {
        $this->inicio = $inicio;

        return $this;
    }

    public function getFinal(): ?string
    {
        return $this->final;
    }

    public function setFinal(string $final): self
    {
        $this->final = $final;

        return $this;
    }

    public function getProvincia(): ?string
    {
        return $this->provincia;
    }

    public function setProvincia(string $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }
}
