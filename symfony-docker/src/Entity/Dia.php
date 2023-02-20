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
    private ?int $valor = null;

    #[ORM\ManyToOne(inversedBy: 'dias')]
    private ?Mes $mes = null;

    #[ORM\Column]
    private ?bool $esLectivo = null;

    public function __construct(int $valor)
    {
        $this->valor = $valor;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValor(): ?int
    {
        return $this->valor;
    }

    public function setValor(int $valor): self
    {
        $this->valor = $valor;

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

    public function isEsLectivo(): ?bool
    {
        return $this->esLectivo;
    }

    public function setEsLectivo(bool $esLectivo): self
    {
        $this->esLectivo = $esLectivo;

        return $this;
    }
}
