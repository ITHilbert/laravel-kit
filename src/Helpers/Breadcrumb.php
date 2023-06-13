<?php
namespace ITHilbert\LaravelKit\Helpers;

class Breadcrumb
{
    private $items = [];

    public function __construct($rootActive = false)
    {
        $rootName = config('laravelkit.breadcrumb.root_name', 'Startseite');
        if($rootActive === true) $this->add($rootName);
        else $this->add($rootName, '/');
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
