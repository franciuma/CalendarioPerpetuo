<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Repository\FestivoNacionalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use App\Interface\FestivoInterface;

#[ORM\Entity(repositoryClass: FestivoNacionalRepository::class)]
#[ORM\Index(name: "inicio_nacional_idx" , fields: ["inicio"])]
class FestivoNacional implements FestivoInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\Column(length: 255)]
    private ?string $abreviatura = null;

    #[ORM\Column(length: 255)]
    private ?string $inicio = null;

    #[ORM\Column(length: 255)]
    private ?string $final = null;

    public function __construct($nombre, $abreviatura, $inicio, $final)
    {
        $this->nombre = $nombre;
        $this->abreviatura = $abreviatura;
        $this->inicio = $inicio;
        $this->final = $final;
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

    public function getAbreviatura(): ?string
    {
        return $this->abreviatura;
    }

    public function setAbreviatura(string $abreviatura): self
    {
        $this->abreviatura = $abreviatura;

        return $this;
    }

    public function getInicio(): string
    {
        return $this->inicio;
    }

    public function setInicio(string $inicio): self
    {
        $this->inicio = $inicio;

        return $this;
    }

    public function getFinal(): string
    {
        return $this->final;
    }

    public function setFinal(string $final): self
    {
        $this->final = $final;

        return $this;
    }
}
