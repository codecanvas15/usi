<?php

namespace App\View\Components;

use App\Models\Purchase;
use Illuminate\View\Component;

class ButtonAuthPrint extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $type,
        public string $href,
        public string $model,
        public string $did,
        public $link = null,
        public string $code,
        public string $label = 'Export',
        public bool $printOption = false,
        public $condition = null,
        public $size = 'md',
        public $symbol = '?',
    ) {
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.button-auth-print');
    }
}
