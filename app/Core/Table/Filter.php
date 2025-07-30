<?php

namespace App\Core\Table;

class Filter
{
    protected ?string $label = null;
    protected ?string $name = null;
    protected ?string $type = null;
    protected $options = null; // Puede ser array o null
    protected $default = null;
    protected $value = null;
    protected bool $includeAllOption = false;
    protected ?string $class = null;
    protected ?string $placeholder = null;  // Placeholder para el input/select

    // Nuevos atributos para dependencia entre combos
    protected ?string $parent = null;        // El nombre del select padre
    protected ?string $parentIdField = 'parent_id'; // El campo en options que indica el padre

    protected ?string $ajaxUrl = null;
    protected ?string $target = null;

    // Define si el filtro usará ajaxUrl para cargar opciones
    public function ajaxUrl(string $ajaxUrl): self
    {
        $this->ajaxUrl = $ajaxUrl;
        return $this;
    }

    // Establece la etiqueta del filtro
    public function target(string $target): self
    {
        $this->target = $target;
        return $this;
    }

    // Método estático para crear una nueva instancia de Filter
    public static function make(string $name): self
    {
        $instance = new self();
        $instance->name = $name;
        return $instance;
    }

    // Establece la etiqueta del filtro
    public function label(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    // Establece el tipo del filtro (input, select, etc.)
    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    // Establece las opciones del filtro
    public function options($options): self
    {
        $this->options = $options;
        return $this;
    }

    public function value($value): self
    {
        $this->value = $value;
        return $this;
    }

    // Establece el valor por defecto del filtro
    public function default($default): self
    {
        $this->default = $default;
        return $this;
    }

    // Define si se debe incluir la opción "Todos" en el select
    public function includeAllOption(bool $include = true): self
    {
        $this->includeAllOption = $include;
        return $this;
    }


    // Establece la clase CSS del filtro
    public function cssClass(string $class): self
    {
        $this->class = $class;
        return $this;
    }

    // Establece el placeholder del filtro
    public function placeholder(string $placeholder): self
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    // Función para establecer el nombre del combo padre
    public function parent(string $parentName = null): self
    {
        $this->parent = $parentName;
        return $this;
    }

    // Función para establecer el campo de referencia en las opciones del combo
    public function parentIdField(string $fieldName = null): self
    {
        $this->parentIdField = $fieldName;
        return $this;
    }

    // Función para establecer la dependencia entre los combos
    public function dependsOn(string $parentName): self
    {
        // Establece que este filtro depende de otro filtro
        $this->parent = $parentName;
        return $this;
    }

    // Convierte el objeto a un array que será devuelto como respuesta
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'name' => $this->name,
            'type' => $this->type,
            'options' => $this->options,
            'default' => $this->default,
            'value' => $this->value,
            'includeAllOption' => $this->includeAllOption,
            'class' => $this->class,
            'placeholder' => $this->placeholder ?? '',
            'parent' => $this->parent,
            'parentIdField' => $this->parentIdField,
            'ajaxUrl' => $this->ajaxUrl ,
            'target' => $this->target
        ];
    }


    // Método predefinido para crear un filtro de tipo "input"
    public static function makeInput(string $name): self
    {
        return self::make($name)
            ->label('')
            ->type('input')
            ->default('')
            ->cssClass('col-6');
    }

    // Método predefinido para crear un filtro de tipo "input"
    public static function makeInputDate(string $name): self
    {
        return self::make($name)
            ->label('')
            ->type('date')
            ->default('')
            ->cssClass('col-6');
    }

    public static function makeInputMonth(string $name): self
    {
        return self::make($name)
            ->label('')
            ->type('date-month')
            ->default('')
            ->cssClass('col-6');
    }

    // Método predefinido para crear un filtro de tipo "input"
    public static function makeInputHidden(string $name): self
    {
        return self::make($name)->type('hidden');
    }

    // Método predefinido para crear un filtro de tipo "select"
    public static function makeSelect(string $name): self
    {
        return self::make($name)
            ->label('')
            ->type('select')
            ->options([])  // Opciones vacías por defecto
            ->default('all')
            ->includeAllOption()
            ->value(null)
            ->cssClass('col-3');
    }
}
