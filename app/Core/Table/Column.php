<?php

namespace App\Core\Table;

class Column
{
    public string $name;
    public ?string $label = null;
    public ?string $field = null;
    public ?string $align = 'left';
    public ?string $width = null;
    public bool $sortable = false;
    public bool $searchable = false;
    public bool $locked = false;
    public string $type = 'text';

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->field = $name;
    }

    public static function make(string $name): self
    {
        return new self($name);
    }

    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function field(string $field): self
    {
        $this->field = $field;
        return $this;
    }

    public function align(string $align): self
    {
        $this->align = $align;
        return $this;
    }

    public function width(string $width): self
    {
        $this->width = $width;
        return $this;
    }

    public function sortable(bool $sortable = true): self
    {
        $this->sortable = $sortable;
        return $this;
    }

    public function searchable(bool $searchable = true): self
    {
        $this->searchable = $searchable;
        return $this;
    }

    public function locked(bool $locked = true): self
    {
        $this->locked = $locked;
        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'label' => $this->label,
            'field' => $this->field,
            'align' => $this->align,
            'width' => $this->width,
            'sortable' => $this->sortable,
            'searchable' => $this->searchable,
            'locked' => $this->locked,
            'type' => $this->type,
        ];
    }

    public static function isDefault(): self
    {
        return self::make('is_default_text')
            ->label(__('¿Is default?'))
            ->align('center')
            ->width('120px')
            ->locked()
            ->sortable(false);
    }

    public static function isActive(): self
    {
        return self::make('is_active_text')
            ->label(__('¿Is active?'))
            ->align('center')
            ->width('120px')
            ->locked()
            ->sortable(false);
    }

    public static function actions(): self
    {
        return self::make('actions')
            ->label(__('Actions'))
            ->align('right')
            ->type('actions')
            ->width('180px')
            ->locked()
            ->sortable(false);
    }
}
