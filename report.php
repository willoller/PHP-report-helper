<?php

require_once('datastore.php');

class report extends datastore
{
    private $_functions = array();

    public function __construct($data_in = null)
    {
        $this->add_default_callbacks();

        $this->_data = $data_in;

        foreach ($this->_data as &$d) {
            $d = (object)$d;
        }
    }

    public function add($func, $callback)
    {
        $this->_functions[$func] = $callback;
    }

    public function rows( $index = null )
    {
        if ($index === null) {
            return $this->_data;
        } else {
            return (object) $this->_data[$index];
        }
    }

    public function __get($name)
    {
        return $this->subgroup($name, $this->_data);
    }

    public function __call($func, $args)
    {
        if (isset($this->_functions[$func])) {
            return call_user_func_array($this->_functions[$func], $args);
        } else {
            return $this->subset($func, $args[0], $this->_data);
        }
    }

    public function subgroup($name, &$data = false)
    {
        $keys = array();
        $newdata = array();
        $data = ($data) ? $data : $this->_data;

        foreach ($data as $d) {
            if (is_array($d)) {
                $keys[$d[$name]] = array();
            } elseif (is_object($d)) {
                $keys[$d->$name] = array();
            }
        }

        foreach ($keys as $key => $null) {
            $newdata[$key] = $this->subset($name, $key, $data);
        }

        return $newdata;
    }

    public function exclude_subgroup($name, &$data = false)
    {
        $keys = array();
        $newdata = array();
        $data = ($data) ? $data : $this->_data;

        foreach ($data as $d) {
            $keys[$d[$name]] = array();
        }

        foreach ($keys as $key => $null) {
            $newdata[$key] = $this->exclude($name, $key, $data);
        }

        return $newdata;
    }

    public function subset($name, $value, &$data = false)
    {
        $newdata = array();
        $data = ($data) ? $data : $this->_data;

        foreach ($data as $d) {
            if (is_array($d) && $d[$name] == $value) {
                $keys[$d[$name]] = array();
                $newdata[] = $d;
            } elseif (is_object($d) && $d->$name == $value) {
                $keys[$d->$name] = array();
                $newdata[] = $d;
            }
        }

        return new self($newdata);
    }

    public function exclude($name, $value, &$data = false)
    {
        $newdata = array();
        $data = ($data) ? $data : $this->_data;
        if (!is_array($value)) $value = array($value);

        foreach ($data as $d) {
            if ((is_array($d) && !in_array($d[$name], $value)) ||
                (is_object($d) && !in_array($d->$name, $value))) {
                $newdata[] = $d;
            }
        }

        return new self($newdata);
    }

    public function add_default_callbacks()
    {
        $this->add('total', function ($key = null, $round = 64) {
            if ($key == null) return null;
            $total = 0;

            foreach ($this->_data as $d) {
                if (is_array($d) && isset($d[$key]))
                    $total += round($d[$key], $round);
                elseif (is_object($d) && $d->$key)
                    $total += round($d->$key, $round);
            }

            return $total;
        });

        $this->add('count', function ($key = null) {
            return (count($this->_data));
        });
    }
}
