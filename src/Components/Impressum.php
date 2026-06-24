<?php

namespace ITHilbert\LaravelKit\Components;

use Illuminate\View\Component;
use ITHilbert\LaravelKit\Contracts\LegalTextPort;

class Impressum extends Component
{
    public ?string $inhalt;

    public function __construct(LegalTextPort $legalText)
    {
        $this->inhalt = $legalText->fetch('impressum');
    }

    public function render()
    {
        return view('laravelkit::components.impressum');
    }
}
