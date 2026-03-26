<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Select extends Component
{
    public $id;

    public $errorBorderId;

    public $errorMessageId;

    public $class;

    public $name;

    public $rounded;

    public $size;

    public $textColor;

    public $selectType;

    public $label;

    public $value;

    public $required;

    public $autofocus;

    public $disabled;

    public $hideAsterix;

    public $hideLabel;

    public $onchange;

    public $onclick;

    public $dataSpecial;

    public $multiple;

    public $helpers;

    public $hasError;

    public $errorMsg;

    public $useBr;

    public function __construct(
        $id = '',
        $errorBorderId = '',
        $errorMessageId = '',
        $class = '',
        $name = '',
        $rounded = 0,
        $size = 'md',
        $textColor = '',
        $selectType = '',
        $label = '',
        $value = '',
        $required = 0,
        $autofocus = 0,
        $disabled = 0,
        $hideAsterix = false,
        $hideLabel = false,
        $hasError = false,
        $errorMsg = '',
        $onchange = '',
        $onclick = '',
        $dataSpecial = '',
        $multiple = '',
        $helpers = '',
        $useBr = false
    ) {
        $this->id = $id;
        $this->errorBorderId = $errorBorderId;
        $this->errorMessageId = $errorMessageId;
        $this->class = $class;
        $this->name = $name;
        $this->rounded = $rounded;
        $this->size = $size;
        $this->textColor = $textColor;
        $this->selectType = $selectType;
        $this->label = $label;
        $this->value = $value;
        $this->required = $required;
        $this->autofocus = $autofocus;
        $this->disabled = $disabled;
        $this->hideAsterix = $hideAsterix;
        $this->hideLabel = $hideLabel;
        $this->hasError = $hasError;
        $this->errorMsg = $errorMsg;
        $this->onchange = $onchange;
        $this->onclick = $onclick;
        $this->dataSpecial = $dataSpecial;
        $this->multiple = $multiple;
        $this->helpers = $helpers;
        $this->useBr = $useBr;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.select');
    }
}
