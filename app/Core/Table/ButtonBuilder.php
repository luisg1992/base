<?php

namespace App\Core\Table;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class ButtonBuilder
{
    protected array $buttons = [];

    public function agregarBoton(Button $button): self
    {
        $this->buttons[] = $button;
        return $this;
    }

    public function agregarBotonesDesdeAcciones(
        AuthenticatableContract $user,
        mixed $valorPermission,
        array $acciones,
        mixed $row = null
    ): self {
        foreach ($acciones as $accion => $boton) {
            $permisoCompleto = "$valorPermission.$accion";
            if ($user->can($permisoCompleto)) {
                $this->agregarBoton(
                    is_callable($boton) ? $boton($row) : $boton
                );
            }
        }

        return $this;
    }

    /**
     * Retorna la configuración de los botones en un array.
     */
    public function getButtons(): array
    {
        return array_map(fn($button) => $button->toArray(), $this->buttons);
    }
}
