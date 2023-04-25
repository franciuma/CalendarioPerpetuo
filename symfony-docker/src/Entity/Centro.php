<?php

namespace App\Entity;

use App\Repository\CentroRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CentroRepository::class)]
class Centro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $provincia = null;

    #[ORM\Column(length: 255)]
    private ?string $inicioDeClases = null;

    #[ORM\ManyToOne(inversedBy: 'centro')]
    private ?Calendario $calendario = null;

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

    public function getProvincia(): ?string
    {
        return $this->provincia;
    }

    public function setProvincia(string $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    public function getInicioDeClases(): ?string
    {
        return $this->inicioDeClases;
    }

    public function setInicioDeClases(string $inicioDeClases): self
    {
        $this->inicioDeClases = $inicioDeClases;

        return $this;
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
}
