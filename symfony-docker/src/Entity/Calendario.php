<?php

namespace App\Entity;

use App\Repository\CalendarioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarioRepository::class)]
class Calendario
{
    public $meses = array(
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    );

    public $diasSemana = array(
        0 => 'Lun',
        1 => 'Mar',
        2 => 'Mie',
        3 => 'Jue',
        4 => 'Vie',
        5 => 'Sab',
        6 => 'Dom'
    );

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'calendario', targetEntity: Anio::class)]
    private Collection $anios;

    #[ORM\OneToMany(mappedBy: 'calendario', targetEntity: Centro::class)]
    private Collection $centro;

    public function __construct()
    {
        $this->anios = new ArrayCollection();
        $this->centro = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMeses(): array
    {
        return $this->meses;
    }

    public function getDiasSemana(): array
    {
        return $this->diasSemana;
    }

    /**
     * @return Collection<int, Anio>
     */
    public function getAnios(): Collection
    {
        return $this->anios;
    }

    public function addAnio(Anio $anio): self
    {
        if (!$this->anios->contains($anio)) {
            $this->anios->add($anio);
            $anio->setCalendario($this);
        }

        return $this;
    }

    public function removeAnio(Anio $anio): self
    {
        if ($this->anios->removeElement($anio)) {
            // set the owning side to null (unless already changed)
            if ($anio->getCalendario() === $this) {
                $anio->setCalendario(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Centro>
     */
    public function getCentro(): Collection
    {
        return $this->centro;
    }

    public function addCentro(Centro $centro): self
    {
        if (!$this->centro->contains($centro)) {
            $this->centro->add($centro);
            $centro->setCalendario($this);
        }

        return $this;
    }

    public function removeCentro(Centro $centro): self
    {
        if ($this->centro->removeElement($centro)) {
            // set the owning side to null (unless already changed)
            if ($centro->getCalendario() === $this) {
                $centro->setCalendario(null);
            }
        }

        return $this;
    }
}
