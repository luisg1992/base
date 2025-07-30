<?php

namespace App\Core\Table;

class Badge
{
    public static function make(string $label, string $color = 'primary'): array
    {
        return [
            'label' => $label,
            'color' => $color,
        ];
    }
}
