<?php

class datastore implements IteratorAggregate, ArrayAccess
{
    protected $_data = array();

    public function getIterator()
    {
        return new ArrayIterator($this->_data);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }
}
