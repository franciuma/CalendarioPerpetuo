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

    #[ORM\OneToMany(mappedBy: 'calendario', targetEntity: FestivoNacional::class)]
    private Collection $festivosNacionales;

    #[ORM\OneToMany(mappedBy: 'calendario', targetEntity: Clase::class)]
    private Collection $clases;

    #[ORM\OneToMany(mappedBy: 'calendario', targetEntity: Anio::class)]
    private Collection $anios;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $provincia = null;

    #[ORM\OneToMany(mappedBy: 'calendario', targetEntity: FestivoLocal::class)]
    private Collection $festivosLocales;

    public function __construct($nombre, $provincia)
    {
        $this->festivosNacionales = new ArrayCollection();
        $this->clases = new ArrayCollection();
        $this->anios = new ArrayCollection();
        $this->nombre = $nombre;
        $this->provincia = $provincia;
        $this->festivosLocales = new ArrayCollection();
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

    /**
     * @return Collection<int, Clase>
     */
    public function getClases(): Collection
    {
        return $this->clases;
    }

    public function addClase(Clase $clase): self
    {
        if (!$this->clases->contains($clase)) {
            $this->clases->add($clase);
            $clase->setCalendario($this);
        }

        return $this;
    }

    public function removeClase(Clase $clase): self
    {
        if ($this->clases->removeElement($clase)) {
            // set the owning side to null (unless already changed)
            if ($clase->getCalendario() === $this) {
                $clase->setCalendario(null);
            }
        }

        return $this;
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
     * @return Collection<int, FestivoLocal>
     */
    public function getFestivosLocales(): Collection
    {
        return $this->festivosLocales;
    }

    public function addFestivoLocal(FestivoLocal $festivoLocal): self
    {
        if (!$this->festivosLocales->contains($festivoLocal)) {
            $this->festivosLocales->add($festivoLocal);
            $festivoLocal->setCalendario($this);
        }

        return $this;
    }

    public function removeFestivoLocal(FestivoLocal $festivoLocal): self
    {
        if ($this->festivosLocales->removeElement($festivoLocal)) {
            // set the owning side to null (unless already changed)
            if ($festivoLocal->getCalendario() === $this) {
                $festivoLocal->setCalendario(null);
            }
        }

        return $this;
    }
}
