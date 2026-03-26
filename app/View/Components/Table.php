<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Table extends Component
{
    public $table_head;

    public $table_body;

    public $theadColor;

    public $class;

    public $id;

    public $isStriped;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($table_head = '', $table_body = '', $theadColor = '', $class = '', $id = '', $isStriped = 'table-striped')
    {
        $this->theadColor = $theadColor;
        $this->table_head = $table_head;
        $this->table_body = $table_body;
        $this->class = $class;
        $this->id = $id;
        $this->isStriped = $isStriped;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.table');
    }
}
