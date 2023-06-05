<?php

namespace App\Entity;

use App\Repository\AnioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnioRepository::class)]
#[ORM\Index(name: "numAnio_idx" , fields: ["numAnio"])]
class Anio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'anio', targetEntity: Mes::class, orphanRemoval: true, cascade: ['remove'])]
    private Collection $mes;

    #[ORM\Column(length: 255)]
    private ?string $numAnio = null;

    #[ORM\ManyToOne(inversedBy: 'anios')]
    private ?Calendario $calendario = null;

    public function __construct($numAnio)
    {
        $this->mes = new ArrayCollection();
        $this->numAnio = $numAnio;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Mes>
     */
    public function getMes(): Collection
    {
        return $this->mes;
    }

    public function addMes(Mes $mes): self
    {
        if (!$this->mes->contains($mes)) {
            $this->mes->add($mes);
            $mes->setAnio($this);
        }

        return $this;
    }

    public function removeMes(Mes $mes): self
    {
        if ($this->mes->removeElement($mes)) {
            // set the owning side to null (unless already changed)
            if ($mes->getAnio() === $this) {
                $mes->setAnio(null);
            }
        }

        return $this;
    }

    public function getNumAnio(): ?string
    {
        return $this->numAnio;
    }

    public function setNumAnio(string $numAnio): self
    {
        $this->numAnio = $numAnio;

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
