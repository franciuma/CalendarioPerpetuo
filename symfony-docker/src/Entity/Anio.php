<?php

namespace App\Entity;

use App\Repository\AnioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnioRepository::class)]
class Anio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'anio', targetEntity: Mes::class, orphanRemoval: true)]
    private Collection $mes;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    public function __construct($nombre)
    {
        $this->mes = new ArrayCollection();
        $this->nombre = $nombre;
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

    public function addMe(Mes $me): self
    {
        if (!$this->mes->contains($me)) {
            $this->mes->add($me);
            $me->setAnio($this);
        }

        return $this;
    }

    public function removeMe(Mes $me): self
    {
        if ($this->mes->removeElement($me)) {
            // set the owning side to null (unless already changed)
            if ($me->getAnio() === $this) {
                $me->setAnio(null);
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
}
