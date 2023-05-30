<?php

namespace App\Entity;

use App\Repository\AsignaturaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AsignaturaRepository::class)]
class Asignatura
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\ManyToOne(inversedBy: 'asignatura')]
    private ?Titulacion $titulacion = null;

    #[ORM\Column(length: 255)]
    private ?string $cuatrimestre = null;

    #[ORM\Column(length: 255)]
    private ?string $abreviatura = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitulacion(): ?Titulacion
    {
        return $this->titulacion;
    }

    public function setTitulacion(?Titulacion $titulacion): self
    {
        $this->titulacion = $titulacion;

        return $this;
    }

    public function getCuatrimestre(): ?string
    {
        return $this->cuatrimestre;
    }

    public function setCuatrimestre(string $cuatrimestre): self
    {
        $this->cuatrimestre = $cuatrimestre;

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
