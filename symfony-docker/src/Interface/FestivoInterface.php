<?php

namespace App\Interface;

use App\Entity\Calendario;

interface FestivoInterface
{
    public function getId(): ?int;
    public function getNombre(): ?string;
    public function getAbreviatura(): ?string;
    public function getInicio(): ?string;
    public function getFinal(): ?string;
}
