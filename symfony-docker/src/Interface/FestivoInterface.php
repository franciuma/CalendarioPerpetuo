<?php

namespace App\Interface;

/**
 * Interfaz para los festivos, dedicada a que un evento pueda definirse como varios tipos de clases.
 * Estas clases ahora mismo son Festivo Nacional y Festivo Local. Hay que ampliarla a Clase (leccion) y Festivo de Centro
 */
interface FestivoInterface
{
    public function getId(): ?int;
    public function getNombre(): ?string;
    public function getAbreviatura(): ?string;
    public function getInicio(): ?string;
    public function getFinal(): ?string;
}
