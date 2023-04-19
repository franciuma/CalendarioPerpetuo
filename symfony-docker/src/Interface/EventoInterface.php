<?php

namespace App\Interface;

/**
 * Interfaz para los festivos y clases, dedicada a que un evento pueda definirse como varios tipos de clases.
 * Estas clases ahora mismo son Festivo Nacional, Festivo Local y clase. Hay que ampliarla a Festivo de Centro
 */
interface EventoInterface
{
    public function getId(): ?int;
    public function getNombre(): ?string;
}
