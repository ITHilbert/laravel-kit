<?php

namespace ITHilbert\LaravelKit\Components;

use Illuminate\View\Component;
use ITHilbert\LaravelKit\Contracts\LegalTextPort;

class CookieRichtlinie extends Component
{
    public ?string $inhalt;

    public function __construct(LegalTextPort $legalText)
    {
        $this->inhalt = $legalText->fetch('cookie-richtlinie');
    }

    public function render()
    {
        return view('laravelkit::components.cookie-richtlinie');
    }
}
