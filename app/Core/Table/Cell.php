<?php

namespace App\Core\Table;

/**
 * Clase Cell: Representa los distintos tipos de celdas que pueden ser renderizadas en el frontend.
 * Soporta estilos y componentes avanzados.
 */
class Cell
{
    /**
     * Texto plano, admite color, tamaño y negrita.
     *
     * @param string $text Contenido textual.
     * @param string|null $color Color del texto (ej: 'red', '#1976d2', 'primary').
     * @param string|null $size Tamaño de fuente (ej: '14px', 'md', '1.2em').
     * @param bool|null $bold Si debe ser negrita.
     * @return array
     */
    public static function text(
        string  $text,
        ?string $color = null,
        ?string $size = null,
        ?bool   $bold = null
    ): array {
        $arr = [
            'type_input' => 'text',
            'value' => $text,
        ];
        if ($color) $arr['color'] = $color;
        if ($size) $arr['size'] = $size;
        if (!is_null($bold)) $arr['bold'] = $bold;
        return $arr;
    }


    /**
     * Badge (etiqueta colorida).
     */
    public static function badge($value, string $label, ?string $color = null, ?string $type = null): array
    {
        return [
            'type_input' => 'badge',
            'value' => $value,
            'label' => $label,
            'color' => $color,
            'type' => $type,
        ];
    }

    /**
     * Ícono (ej: FontAwesome, Material).
     */
    public static function icon(string $icon, ?string $color = null, ?string $tooltip = null): array
    {
        $arr = [
            'type_input' => 'icon',
            'icon' => $icon,
        ];
        if ($color) $arr['color'] = $color;
        if ($tooltip) $arr['tooltip'] = $tooltip;
        return $arr;
    }

    /**
     * Link (enlace), opcional con ícono.
     */
    public static function link(string $label, string $url, ?string $target = null, ?string $icon = null): array
    {
        $arr = [
            'type_input' => 'link',
            'label' => $label,
            'url' => $url,
        ];
        if ($target) $arr['target'] = $target;
        if ($icon) $arr['icon'] = $icon;
        return $arr;
    }

    /**
     * Chip (tipo badge visual, ej: Quasar, Vuetify).
     */
    public static function chip(string $label, string $color = 'primary', ?string $icon = null): array
    {
        $arr = [
            'type_input' => 'chip',
            'label' => $label,
            'color' => $color,
        ];
        if ($icon) $arr['icon'] = $icon;
        return $arr;
    }

    /**
     * Avatar (imagen de usuario o similar).
     * @param string $src URL de la imagen
     * @param string|null $alt Texto alternativo
     * @param string|null $size Tamaño (ej: '32px')
     */
    public static function avatar(string $src, ?string $alt = null, ?string $size = null): array
    {
        $arr = [
            'type_input' => 'avatar',
            'src' => $src,
        ];
        if ($alt) $arr['alt'] = $alt;
        if ($size) $arr['size'] = $size;
        return $arr;
    }

    /**
     * Switch (toggle on/off).
     * @param bool $checked Estado inicial
     * @param string|null $color Color del switch
     * @param bool $readonly Si solo es visual o se puede cambiar (opcional)
     */
    public static function switch(bool $checked, ?string $color = null, bool $readonly = true): array
    {
        $arr = [
            'type_input' => 'switch',
            'checked' => $checked,
            'readonly' => $readonly,
        ];
        if ($color) $arr['color'] = $color;
        return $arr;
    }

    /**
     * Celda compuesta: varias líneas y/o elementos combinados (texto, badge, ícono, chip, etc).
     * @param array $lines Cada línea es un array de elementos Cell.
     */
    public static function composite(array $lines): array
    {
        return [
            'type_input' => 'composite',
            'lines' => $lines,
        ];
    }

    /**
     * Multi-línea solo texto.
     * @param array|string $lines Arreglo de líneas o string separado por \n.
     */
    public static function multiLine($lines): array
    {
        if (is_string($lines)) {
            $lines = explode("\n", $lines);
        }
        return [
            'type_input' => 'multi_line',
            'value' => $lines,
        ];
    }

    /**
     * Badge text
     */
    public static function badgeText(string $label, ?string $type = 'success'): array
    {
        return [
            'type_input' => 'badge',
            'label' => $label,
            'type' => $type,
        ];
    }

    /**
     * Badge activo/inactivo.
     */
    public static function badgeEstado($row, string $yesText = 'Activo', string $noText = 'Inactivo'): array
    {
        $possibleFields = [
            'is_active',
            'estado',
            'Estado',
            'idEstado',
            'IdEstado',
            'idestado',
            'esActivo',
            'EsActivo',
            'IdEstado'
        ];

        $isActive = false;

        foreach ($possibleFields as $field) {
            if (is_array($row) && array_key_exists($field, $row)) {
                $isActive = (bool) $row[$field];
                break;
            }

            if (is_object($row) && isset($row->{$field})) {
                $isActive = (bool) $row->{$field};
                break;
            }
        }

        return self::badge(
            $isActive,
            $isActive ? $yesText : $noText,
            null,
            $isActive ? 'success' : 'danger'
        );
    }
}
