<?php

namespace ITHilbert\LaravelKit\Components;

use Illuminate\View\Component;
use GuzzleHttp\Client;

class AGB extends Component
{

    public $agbInhalt;

    public function __construct()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://center.it-hilbert.com/api/agb');
        $this->agbInhalt = json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.agb');
    }
}
