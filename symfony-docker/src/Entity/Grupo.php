<?php

namespace App\Entity;

use App\Repository\GrupoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GrupoRepository::class)]
class Grupo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $letra = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete:"CASCADE")]
    private ?Asignatura $asignatura = null;

    #[ORM\Column(length: 255)]
    private ?string $horario = null;

    #[ORM\Column(type: Types::JSON)]
    private array $dias_teoria = [];

    #[ORM\Column(type: Types::JSON)]
    private array $dias_practica = [];

    #[ORM\OneToMany(mappedBy: 'grupo', targetEntity: Clase::class)]
    private Collection $clases;

    #[ORM\OneToMany(mappedBy: 'grupoD', targetEntity: UsuarioGrupo::class, orphanRemoval: true, cascade:["remove"])]
    private Collection $usuarioGrupos;

    public function __construct()
    {
        $this->clases = new ArrayCollection();
        $this->usuarioGrupos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLetra(): ?string
    {
        return $this->letra;
    }

    public function setLetra(string $letra): self
    {
        $this->letra = $letra;

        return $this;
    }

    public function getAsignatura(): ?Asignatura
    {
        return $this->asignatura;
    }

    public function setAsignatura(?Asignatura $asignatura): self
    {
        $this->asignatura = $asignatura;

        return $this;
    }

    public function getHorario(): ?string
    {
        return $this->horario;
    }

    public function setHorario(string $horario): self
    {
        $this->horario = $horario;

        return $this;
    }

    public function getDiasTeoria(): array
    {
        return $this->dias_teoria;
    }

    public function setDiasTeoria(array $dias_teoria): self
    {
        $this->dias_teoria = $dias_teoria;

        return $this;
    }

    public function getDiasPractica(): array
    {
        return $this->dias_practica;
    }

    public function setDiasPractica(array $dias_practica): self
    {
        $this->dias_practica = $dias_practica;

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
            $clase->setGrupo($this);
        }

        return $this;
    }

    public function removeClase(Clase $clase): self
    {
        if ($this->clases->removeElement($clase)) {
            // set the owning side to null (unless already changed)
            if ($clase->getGrupo() === $this) {
                $clase->setGrupo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, UsuarioGrupo>
     */
    public function getUsuarioGrupos(): Collection
    {
        return $this->usuarioGrupos;
    }

    public function addUsuarioGrupo(UsuarioGrupo $usuarioGrupo): static
    {
        if (!$this->usuarioGrupos->contains($usuarioGrupo)) {
            $this->usuarioGrupos->add($usuarioGrupo);
            $usuarioGrupo->setGrupo($this);
        }

        return $this;
    }

    public function removeUsuarioGrupo(UsuarioGrupo $usuarioGrupo): static
    {
        if ($this->usuarioGrupos->removeElement($usuarioGrupo)) {
            // set the owning side to null (unless already changed)
            if ($usuarioGrupo->getGrupo() === $this) {
                $usuarioGrupo->setGrupo(null);
            }
        }

        return $this;
    }
}
