<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ModalDelete extends Component
{
    public $title;

    public $text;

    public $footer;

    public $id;

    public $url;

    public $dataId;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = 'Are you sure to do this action ?', $text = "You'll lose your data, this action can't be undone.", $footer = '', $id = '', $url = '', $dataId = '')
    {
        $this->title = $title;
        $this->text = $text;
        $this->footer = $footer;
        $this->id = $id;
        $this->url = $url;
        $this->dataId = $dataId;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.modal-delete');
    }
}
