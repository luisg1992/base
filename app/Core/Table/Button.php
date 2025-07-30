<?php

namespace App\Core\Table;

class Button
{
    protected ?string $label = null;
    protected ?string $icon = null;
    protected ?string $action = null;
    protected ?string $color = null;
    protected bool $disable = false;
    protected bool $permission = true;
    protected ?string $url = null;
    protected bool $separator = false;

    /**
     * Método estático para crear una instancia.
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Define la Etiqueta (label) del botón.
     */
    public function label(?string $label): self
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Define el icono del botón.
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Define la acción asociada al botón.
     */
    public function action(string $action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Define el color del botón.
     */
    public function color(string $color): self
    {
        $this->color = $color;
        return $this;
    }

    /**
     * Define si el botón debe estar deshabilitado.
     */
    public function disable(bool $disable): self
    {
        $this->disable = $disable;
        return $this;
    }

    /**
     * Define si el botón tiene permiso.
     */
    public function permission(bool $permission): self
    {
        $this->permission = $permission;
        return $this;
    }

    /**
     * Define la URL del botón (opcional).
     */
    public function url(?string $url): self
    {
        $this->url = $url;
        return $this;
    }

    /**
     * Define la URL del botón (opcional).
     */
    public function separator(?bool $separator): self
    {
        $this->separator = $separator;
        return $this;
    }

    /**
     * Convierte la configuración del botón a un array.
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'icon' => $this->icon,
            'action' => $this->action,
            'color' => $this->color,
            'disable' => $this->disable,
            'separator' => $this->separator,
            'permission' => $this->permission,
            'url' => $this->url,
        ];
    }

    public static function botonRecargar($url = null): self
    {
        return self::make()
            ->label(null)
            ->icon('refresh')
            ->action('refresh')
            ->color('secondary')
            ->disable(false)
            ->url($url);
    }

    public static function botonCrear($url = null): self
    {
        return self::make()
            ->label(null)
            ->icon('plus')
            ->action('new')
            ->color('primary')
            ->disable(false)
            ->url($url);
    }

    public static function botonVisualizar($separator = false): self
    {
        return self::make()
            ->label('Ver')
            ->icon('ti ti-eye')
            ->action("view")
            ->disable(false)
            ->separator($separator)
            ->url(null);
    }

    public static function botonEditar($separator = false): self
    {
        return self::make()
            ->label('Editar')
            ->icon('ti ti-pencil')
            ->action("edit")
            ->disable(false)
            ->separator($separator)
            ->url(null);
    }

    public static function botonEliminar($separator = false): self
    {
        return self::make()
            ->label('Eliminar')
            ->icon('ti ti-trash')
            ->color('red')
            ->action("delete")
            ->disable(false)
            ->separator($separator)
            ->url(null);
    }

    public static function botonEstado($estado, $separator = false): self
    {
        return self::make()
            ->label($estado ? 'Deshabilitar' : 'Habilitar')
            ->icon($estado ? 'ti ti-user-cancel' : 'ti ti-user-star')
            ->action("active")
            ->disable(false)
            ->separator($separator)
            ->url(null);
    }
}
