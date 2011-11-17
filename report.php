<?php

class Report
{
    private $_data = array();

    public function __construct($data_in = null)
    {
        if ($data_in !== null)
            $this->_data = $data_in;
    }

    public function __call($func, $args)
    {
        return $this->subset($func, $args[0], $this->_data);
    }

    public function subset($name, $value, &$data)
    {
        $newdata = array();
        $data = ($data) ? $data : $this->_data;

        foreach ($data as $d)
            if ($d[$name] == $value)
                $newdata[] = $d;

        return new self($newdata);
    }

    /*
    public function total($key, &$data = false)
    {
        $total = 0;
        $data = ($data) ? $data : $this->_data;

        foreach ($data as $d)
            $total += $d[$key];
    }
     */
    public function total($key)
    {
        $total = 0;

        foreach ($this->_data as $d)
            $total += $d[$key];

        return $total;
    }

    public function count($key = null)
    {
        $count = 0;

        foreach ($this->_data as $d)
            $count++;

        return $count;
    }
}
