<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Select extends Component
{
    public string $id;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public Collection $options,
        public string $defaultValue = '',
        public string $label = ''
    ) {
        $this->id = $name.rand(10, 100000);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form.select');
    }
}
