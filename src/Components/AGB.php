<?php

namespace ITHilbert\LaravelKit\Components;

use Illuminate\View\Component;
use ITHilbert\LaravelKit\Contracts\LegalTextPort;

class AGB extends Component
{
    public ?string $inhalt;

    public function __construct(LegalTextPort $legalText)
    {
        $this->inhalt = $legalText->fetch('agb');
    }

    public function render()
    {
        return view('laravelkit::components.agb');
    }
}
