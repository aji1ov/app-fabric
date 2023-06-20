<?php

namespace App\Fabric\Model;

class ValueSource
{
    private array $data;
    public function __construct(array $data)
    {
        //print_r(['value_source'=>$data]);
        $this->data = $data;
    }

    public function get(string $key)
    {
        if($val = $this->data[$key]) return $val;
        if($val = $this->data[strtolower($key)]) return $val;
        if($val = $this->data[strtoupper($key)]) return $val;
        return null;
    }

    public function set(string $key, mixed $value)
    {
        $this->data[$key] = $value;
    }

    public function fork(string $startWith): ValueSource
    {
        $fork = [];
        foreach($this->data as $key => $value)
        {
            if(str_starts_with($key, $startWith))
            {
                $fork[substr($key, strlen($startWith))] = $value;
            }
        }

        return new ValueSource($fork);
    }

    public function isEmpty()
    {
        return !count($this->data);
    }

    public function getArray()
    {
        return $this->data;
    }
}