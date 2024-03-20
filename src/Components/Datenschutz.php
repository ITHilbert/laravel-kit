<?php

namespace ITHilbert\LaravelKit\Components;

use Illuminate\View\Component;
use GuzzleHttp\Client;

class Datenschutz extends Component
{

    public $datenschutzInhalt;

    public function __construct()
    {
        try {
            $client = new Client();
            $response = $client->request('GET', 'https://center.it-hilbert.com/api/datenschutz');
            $this->datenschutzInhalt = json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            //$this->datenschutzInhalt = ['error' => $e->getMessage()];
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('laravelkit::components.datenschutz');
    }
}
