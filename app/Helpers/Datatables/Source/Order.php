<?php


namespace App\Helpers\Datatables\Source;


class Order
{

    static public function fromArray(?array $array = null)
    {
        $instance = new Order();
        $array = $array ?? [];
        $instance->column = isset($array['column']) ? $array['column'] : 0;
        $instance->dir = isset($array['dir']) ? $array['dir'] : 'asc';

        return $instance;
    }

    protected $column;

    protected $dir;

    public function column()
    {
        return $this->column;
    }

    public function dir()
    {
        return $this->dir;
    }
}
