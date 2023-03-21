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
    private ?bool $esLectivo = false;

    #[ORM\Column(length: 255)]
    private ?string $fecha = null;

    #[ORM\Column(length: 255)]
    private ?string $nombreDiaDeLaSemana = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?FestivoNacional $evento = null;

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

    public function esLectivo(): ?bool
    {
        return $this->esLectivo;
    }

    public function setEsLectivo(bool $esLectivo): self
    {
        $this->esLectivo = $esLectivo;

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

    public function getEvento(): ?FestivoNacional
    {
        return $this->evento;
    }

    public function setEvento(?FestivoNacional $evento): self
    {
        $this->evento = $evento;

        return $this;
    }
}
