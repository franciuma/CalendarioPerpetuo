<?php

namespace App\Entity;

use App\Repository\MesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MesRepository::class)]
class Mes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'mes', targetEntity: Dia::class, cascade: ['remove'])]
    private Collection $dias;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $numMes = null;

    #[ORM\Column]
    private ?int $primerDia = null;

    #[ORM\ManyToOne(inversedBy: 'mes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Anio $anio = null;

    public function __construct($numMes)
    {
        $this->dias = new ArrayCollection();
        $this->numMes = $numMes;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Dia>
     */
    public function getDias(): Collection
    {
        return $this->dias;
    }

    public function addDia(Dia $dia): self
    {
        if (!$this->dias->contains($dia)) {
            $this->dias->add($dia);
            $dia->setMes($this);
        }

        return $this;
    }

    public function removeDia(Dia $dia): self
    {
        if ($this->dias->removeElement($dia)) {
            // set the owning side to null (unless already changed)
            if ($dia->getMes() === $this) {
                $dia->setMes(null);
            }
        }

        return $this;
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

    public function getNumMes(): ?string
    {
        return $this->numMes;
    }

    public function setNumMes(string $numMes): self
    {
        $this->numMes = $numMes;

        return $this;
    }

    public function getPrimerDia(): ?int
    {
        return $this->primerDia;
    }

    public function setPrimerDia(int $primerDia): self
    {
        $this->primerDia = $primerDia;

        return $this;
    }

    public function getAnio(): ?Anio
    {
        return $this->anio;
    }

    public function setAnio(?Anio $anio): self
    {
        $this->anio = $anio;

        return $this;
    }
}
