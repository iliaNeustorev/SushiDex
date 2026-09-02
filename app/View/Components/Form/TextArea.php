<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TextArea extends Component
{
    public string $id;

    public function __construct(
        public string $name,
        public string $label = '',
        public string $placeholder = '',
        public string $rows = '3',
        public string $defaultValue = ''
    ) {
        $this->id = $name.rand(10, 100000);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.text-area');
    }
}
