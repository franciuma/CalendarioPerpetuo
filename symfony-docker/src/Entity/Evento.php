<?php

namespace App\Entity;

use App\Repository\EventoRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Interface\EventoInterface;

/**
 * Clase Evento:
 * Un evento puede ser un festivo Nacional, un festivo local, un festivo de un centro, o una clase (lección).
 */
#[ORM\Entity(repositoryClass: EventoRepository::class)]
class Evento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?FestivoNacional $festivoNacional = null;

    #[ORM\ManyToOne]
    private ?FestivoLocal $festivoLocal = null;

    #[ORM\ManyToOne(inversedBy: 'eventos', cascade:['remove'])]
    private ?Clase $clase = null;

    #[ORM\ManyToOne]
    private ?FestivoCentro $festivoCentro = null;

    #[ORM\ManyToOne(inversedBy: 'eventos')]
    private ?Dia $dia = null;

    public function __construct(?EventoInterface $evento = null)
    {
        if ($evento instanceof FestivoNacional) {
            $this->setFestivoNacional($evento);
        } elseif ($evento instanceof FestivoLocal) {
            $this->setFestivoLocal($evento);
        } elseif ($evento instanceof Clase){
            $this->setClase($evento);
        } elseif ($evento instanceof FestivoCentro) {
            $this->setFestivoCentro($evento);
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreFestivo(): ?string
    {
        if ($this->festivoNacional) {
            return $this->festivoNacional->getNombre();
        }
        if ($this->festivoLocal) {
            return $this->festivoLocal->getNombre();
        }
        if($this->festivoCentro) {
            return $this->festivoCentro->getNombre();
        }

        return null;
    }

    public function getNombreClase(): ?string
    {
        if($this->clase){
            return $this->clase->getNombre();
        }

        return null;
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

    public function getFestivoCentro(): ?FestivoCentro
    {
        return $this->festivoCentro;
    }

    public function setFestivoCentro(?FestivoCentro $festivoCentro): self
    {
        $this->festivoCentro = $festivoCentro;

        return $this;
    }

    public function getClase(): ?Clase
    {
        return $this->clase;
    }

    public function setClase(?Clase $clase): static
    {
        $this->clase = $clase;

        return $this;
    }

}
