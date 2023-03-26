<?php

namespace App\Entity;

use App\Repository\DiaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiaRepository::class)]
class Dia
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $numDia = null;

    #[ORM\ManyToOne(inversedBy: 'dias')]
    private ?Mes $mes = null;

    #[ORM\Column]
    private ?bool $esNoLectivo = false;

    #[ORM\Column(length: 255)]
    private ?string $fecha = null;

    #[ORM\Column(length: 255)]
    private ?string $nombreDiaDeLaSemana = null;

    #[ORM\OneToOne(mappedBy: 'dia', cascade: ['persist', 'remove'])]
    private ?Evento $evento = null;

    public function __construct(string $numDia)
    {
        $this->numDia = $numDia;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumDia(): ?string
    {
        return $this->numDia;
    }

    public function setNumDia(string $numDia): self
    {
        $this->numDia = $numDia;

        return $this;
    }

    public function getMes(): ?Mes
    {
        return $this->mes;
    }

    public function setMes(?Mes $mes): self
    {
        $this->mes = $mes;

        return $this;
    }

    public function esNoLectivo(): ?bool
    {
        return $this->esNoLectivo;
    }

    public function setEsNoLectivo(bool $esNoLectivo): self
    {
        $this->esNoLectivo = $esNoLectivo;

        return $this;
    }

    public function getFecha(): ?string
    {
        return $this->fecha;
    }

    public function setFecha(string $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getNombreDiaDeLaSemana(): ?string
    {
        return $this->nombreDiaDeLaSemana;
    }

    public function setNombreDiaDeLaSemana(string $nombreDiaDeLaSemana): self
    {
        $this->nombreDiaDeLaSemana = $nombreDiaDeLaSemana;

        return $this;
    }

    public function getEvento(): ?Evento
    {
        return $this->evento;
    }

    public function setEvento(?Evento $evento): self
    {
        // unset the owning side of the relation if necessary
        if ($evento === null && $this->evento !== null) {
            $this->evento->setDia(null);
        }

        // set the owning side of the relation if necessary
        if ($evento !== null && $evento->getDia() !== $this) {
            $evento->setDia($this);
        }

        $this->evento = $evento;

        return $this;
    }
}
