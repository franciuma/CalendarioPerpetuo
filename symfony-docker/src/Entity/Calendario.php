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
        0 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo'
    );

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $mes;

    #[ORM\Column]
    private string $anio;

    #[ORM\OneToMany(mappedBy: 'calendario', targetEntity: FestivoNacional::class)]
    private Collection $festivosNacionales;

    public function __construct($anio)
    {
        $this->anio = $anio;
        $this->festivosNacionales = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMes(): string
    {
        return $this->mes;
    }

    public function getMeses(): array
    {
        return $this->meses;
    }

    public function getAnio(): string
    {
        return $this->anio;
    }

    public function getDiasSemana(): array
    {
        return $this->diasSemana;
    }

    /**
     * @return Collection<int, FestivoNacional>
     */
    public function getFestivosNacionales(): Collection
    {
        return $this->festivosNacionales;
    }

    public function addFestivosNacionale(FestivoNacional $festivosNacionale): self
    {
        if (!$this->festivosNacionales->contains($festivosNacionale)) {
            $this->festivosNacionales->add($festivosNacionale);
            $festivosNacionale->setCalendario($this);
        }

        return $this;
    }

    public function removeFestivosNacionale(FestivoNacional $festivosNacionale): self
    {
        if ($this->festivosNacionales->removeElement($festivosNacionale)) {
            // set the owning side to null (unless already changed)
            if ($festivosNacionale->getCalendario() === $this) {
                $festivosNacionale->setCalendario(null);
            }
        }

        return $this;
    }
}
