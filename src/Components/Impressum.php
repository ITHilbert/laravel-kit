<?php

namespace ITHilbert\LaravelKit\Components;

use Illuminate\View\Component;
use GuzzleHttp\Client;

class Impressum extends Component
{

    public $impressumInhalt;

    public function __construct()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://center.it-hilbert.com/api/impressum');
        $this->impressumInhalt = json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.impressum');
    }
}
