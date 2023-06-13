<?php
namespace ITHilbert\LaravelKit\Helpers;

class Breadcrumb
{
    private $items = [];

    public function __construct($rootActive = false)
    {
        if($rootActive === true) $this->add('Startseite');
        else $this->add('Startseite', '/');
    }

    public function add($title, $url = '#')
    {
        $item = [
            'url' => $url,
            'title' => $title,
        ];

        $this->items[] = $item;
    }

    public function getItems()
    {
        return $this->items;
    }
}
