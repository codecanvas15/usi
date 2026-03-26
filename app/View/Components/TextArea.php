<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TextArea extends Component
{
    public $id;

    public $class;

    public $name;

    public $type;

    public $rounded;

    public $size;

    public $textColor;

    public $leftIcon;

    public $rightIcon;

    public $classIcon;

    public $styleIcon;

    public $fontawesome;

    public $label;

    public $value;

    public $placeholder;

    public $autocomplete;

    public $required;

    public $autofocus;

    public $disabled;

    public $list;

    public $multiple;

    public $hideAsterix;

    public $onchange;

    public $onclick;

    public $onkeyup;

    public function __construct(
        $id = '',
        $class = '',
        $name = '',
        $type = 'text',
        $rounded = 0,
        $size = 'md',
        $textColor = '',
        $leftIcon = '',
        $rightIcon = '',
        $classIcon = '',
        $styleIcon = 'fas',
        $fontawesome = '',
        $label = '',
        $value = '',
        $placeholder = '',
        $autocomplete = '',
        $required = 0,
        $autofocus = 0,
        $disabled = 0,
        $hideAsterix = false,
        $onchange = '',
        $onclick = '',
        $onkeyup = ''
    ) {
        $this->id = $id;
        $this->class = $class;
        $this->name = $name;
        $this->type = $type;
        $this->rounded = $rounded;
        $this->size = $size;
        $this->textColor = $textColor;
        $this->leftIcon = $leftIcon;
        $this->rightIcon = $rightIcon;
        $this->classIcon = $classIcon;
        $this->styleIcon = $styleIcon;
        $this->fontawesome = $fontawesome;
        $this->label = $label;
        $this->value = $value;
        $this->placeholder = $placeholder;
        $this->autocomplete = $autocomplete;
        $this->required = $required;
        $this->autofocus = $autofocus;
        $this->disabled = $disabled;
        $this->hideAsterix = $hideAsterix;
        $this->onchange = $onchange;
        $this->onclick = $onclick;
        $this->onkeyup = $onkeyup;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.text-area');
    }
}
