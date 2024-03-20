<?php

namespace ITHilbert\LaravelKit\Components;

use Illuminate\View\Component;
use GuzzleHttp\Client;

class CookieRichtlinie extends Component
{

    public $cookieInhalt;

    public function __construct()
    {
        try {
            $client = new Client();
            $response = $client->request('GET', 'https://center.it-hilbert.com/api/cookie-richtlinie');
            $this->cookieInhalt = json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            //$this->cookieInhalt = ['error' => $e->getMessage()];
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('laravelkit::components.cookie-richtlinie');
    }
}
