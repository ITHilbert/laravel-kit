<?php

namespace ITHilbert\LaravelKit\Components;

use Illuminate\View\Component;
use ITHilbert\LaravelKit\Contracts\LegalTextPort;

class Datenschutz extends Component
{
    public ?string $inhalt;

    public function __construct(LegalTextPort $legalText)
    {
        $this->inhalt = $legalText->fetch('datenschutz');
    }

    public function render()
    {
        return view('laravelkit::components.datenschutz');
    }
}
