<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CardDataTable extends Component
{
    public $title;

    public $footer;

    public $breadcrumbs;

    public $header_content;

    public $table_content;

    public $id;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title = '', $footer = '', $breadcrumbs = '', $header_content = '', $table_content = '', $id = '')
    {
        $this->title = $title;
        $this->footer = $footer;
        $this->breadcrumbs = $breadcrumbs;
        $this->header_content = $header_content;
        $this->table_content = $table_content;
        $this->id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.card-data-table');
    }
}
