<?php

namespace App\Entity;

use App\Repository\DiaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\Column]
    private ?bool $hayClase = false;

    #[ORM\OneToMany(mappedBy: 'dia', targetEntity: Evento::class, cascade: ['persist', 'remove'])]
    private Collection $eventos;

    public function __construct(string $numDia)
    {
        $this->numDia = $numDia;
        $this->eventos = new ArrayCollection();
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

    public function hayClase(): ?bool
    {
        return $this->hayClase;
    }

    public function setHayClase(bool $hayClase): self
    {
        $this->hayClase = $hayClase;

        return $this;
    }

    /**
     * @return Collection<int, Evento>
     */
    public function getEventos(): Collection
    {
        return $this->eventos;
    }

    public function addEvento(Evento $evento): self
    {
        if (!$this->eventos->contains($evento)) {
            $this->eventos->add($evento);
            $evento->setDia($this);
        }

        return $this;
    }

    public function removeEvento(Evento $evento): self
    {
        if ($this->eventos->removeElement($evento)) {
            // set the owning side to null (unless already changed)
            if ($evento->getDia() === $this) {
                $evento->setDia(null);
            }
        }

        return $this;
    }
}
