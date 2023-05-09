<?php

namespace App\Entity;

use App\Repository\TitulacionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TitulacionRepository::class)]
class Titulacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombreTitulacion = null;

    #[ORM\OneToMany(mappedBy: 'titulacion', targetEntity: Asignatura::class)]
    private Collection $asignatura;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Centro $centro = null;

    public function __construct()
    {
        $this->asignatura = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreTitulacion(): ?string
    {
        return $this->nombreTitulacion;
    }

    public function setNombreTitulacion(string $nombreTitulacion): self
    {
        $this->nombreTitulacion = $nombreTitulacion;

        return $this;
    }

    /**
     * @return Collection<int, Asignatura>
     */
    public function getAsignatura(): Collection
    {
        return $this->asignatura;
    }

    public function addAsignatura(Asignatura $asignatura): self
    {
        if (!$this->asignatura->contains($asignatura)) {
            $this->asignatura->add($asignatura);
            $asignatura->setTitulacion($this);
        }

        return $this;
    }

    public function removeAsignatura(Asignatura $asignatura): self
    {
        if ($this->asignatura->removeElement($asignatura)) {
            // set the owning side to null (unless already changed)
            if ($asignatura->getTitulacion() === $this) {
                $asignatura->setTitulacion(null);
            }
        }

        return $this;
    }

    public function getCentro(): ?Centro
    {
        return $this->centro;
    }

    public function setCentro(?Centro $centro): self
    {
        $this->centro = $centro;

        return $this;
    }
}
