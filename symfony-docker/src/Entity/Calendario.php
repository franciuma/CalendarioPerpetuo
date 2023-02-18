<?php

namespace App\Entity;

use App\Repository\CalendarioRepository;
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
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado'
    );

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $mes;

    #[ORM\Column]
    private string $anio;

    public function __construct($mes,$anio)
    {
        $this->mes = $mes;
        $this->anio = $anio;
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
    //FALTA DECLARARLO COMO VARIABLE PRIVATE.
}
