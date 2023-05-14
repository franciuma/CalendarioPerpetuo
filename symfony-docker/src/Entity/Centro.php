<?php

namespace App\Entity;

use App\Repository\CentroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CentroRepository::class)]
class Centro
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $provincia = null;

    #[ORM\OneToMany(mappedBy: 'centro', targetEntity: Calendario::class)]
    private Collection $calendarios;

    public function __construct()
    {
        $this->calendarios = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getProvincia(): ?string
    {
        return $this->provincia;
    }

    public function setProvincia(string $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }

    /**
     * @return Collection<int, Calendario>
     */
    public function getCalendarios(): Collection
    {
        return $this->calendarios;
    }

    public function addCalendario(Calendario $calendario): self
    {
        if (!$this->calendarios->contains($calendario)) {
            $this->calendarios->add($calendario);
            $calendario->setCentro($this);
        }

        return $this;
    }

    public function removeCalendario(Calendario $calendario): self
    {
        if ($this->calendarios->removeElement($calendario)) {
            // set the owning side to null (unless already changed)
            if ($calendario->getCentro() === $this) {
                $calendario->setCentro(null);
            }
        }

        return $this;
    }
}
