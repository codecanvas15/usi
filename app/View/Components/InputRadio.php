<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InputRadio extends Component
{
    public $id;

    public $class;

    public $name;

    public $textColor;

    public $color;

    public $label;

    public $value;

    public $required;

    public $disabled;

    public $hideAsterix;

    public $onchange;

    public $checked;

    public $onclick;

    public function __construct(
        $id = '',
        $class = '',
        $name = '',
        $textColor = '',
        $color = '',
        $label = '',
        $value = '',
        $checked = 0,
        $required = 0,
        $disabled = 0,
        $hideAsterix = false,
        $onchange = '',
        $onclick = ''
    ) {
        $this->id = $id;
        $this->class = $class;
        $this->name = $name;
        $this->textColor = $textColor;
        $this->color = $color;
        $this->label = $label;
        $this->value = $value;
        $this->checked = $checked;
        $this->required = $required;
        $this->disabled = $disabled;
        $this->hideAsterix = $hideAsterix;
        $this->onchange = $onchange;
        $this->onclick = $onclick;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.input-radio');
    }
}
