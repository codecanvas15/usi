<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Modal extends Component
{
    public $title;

    public $id;

    public $headerColor;

    public $modal_footer;

    public $modalSize;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = '', $id = '', $headerColor = 'secondary', $modal_footer = '', $modalSize = '')
    {
        $this->title = $title;
        $this->id = $id;
        $this->headerColor = $headerColor;
        $this->modal_footer = $modal_footer;
        $this->modalSize = $modalSize;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modal');
    }
}
