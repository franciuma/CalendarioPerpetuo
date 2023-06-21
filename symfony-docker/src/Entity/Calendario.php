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

    #[ORM\OneToMany(mappedBy: 'calendario', targetEntity: Anio::class, cascade: ['remove'])]
    private Collection $anios;

    #[ORM\OneToOne]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'calendarios')]
    private ?Centro $centro = null;

    public function __construct(Usuario $usuario, Centro $centro)
    {
        $this->anios = new ArrayCollection();
        $this->usuario = $usuario;
        $this->centro = $centro;
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

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;

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
