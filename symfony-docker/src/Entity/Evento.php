<?php

namespace App\Entity;

use App\Repository\EventoRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Interface\FestivoInterface;

#[ORM\Entity(repositoryClass: EventoRepository::class)]
class Evento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?FestivoNacional $festivoNacional = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?FestivoLocal $festivoLocal = null;

    #[ORM\OneToOne(inversedBy: 'evento', cascade: ['persist', 'remove'])]
    private ?Dia $dia = null;

    public function __construct(?FestivoInterface $festivo = null)
    {
        if ($festivo instanceof FestivoNacional) {
            $this->setFestivoNacional($festivo);
        } elseif ($festivo instanceof FestivoLocal) {
            $this->setFestivoLocal($festivo);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreFestivo(): ?string
    {
        if ($this->festivoNacional) {
            return $this->festivoNacional->getAbreviatura();
        }
        if ($this->festivoLocal) {
            return $this->festivoLocal->getAbreviatura();
        }

        return null;
    }

    public function getFestivoNacional(): ?FestivoNacional
    {
        return $this->festivoNacional;
    }

    public function setFestivoNacional(?FestivoNacional $festivoNacional): self
    {
        $this->festivoNacional = $festivoNacional;

        return $this;
    }

    public function getFestivoLocal(): ?FestivoLocal
    {
        return $this->festivoLocal;
    }

    public function setFestivoLocal(?FestivoLocal $festivoLocal): self
    {
        $this->festivoLocal = $festivoLocal;

        return $this;
    }

    public function getDia(): ?Dia
    {
        return $this->dia;
    }

    public function setDia(?Dia $dia): self
    {
        $this->dia = $dia;

        return $this;
    }
}
