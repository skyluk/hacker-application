<?php

class Application_Model_Entry
{
    protected $_id;             // the ID of the entry
    protected $_description;    // generic description of the entry
    protected $_drivetime;      // approx drive time (hours)
    protected $_miles;          // miles in the trip
    protected $_cost;           // cost of fuel, per gallon
    protected $_mpg;            // approx fuel economy

    public function __construct(array $opts = null) {
        if( is_array($opts) )
            $this->setOpts($opts);
    }

    public function __set($name, $value) {
        $method = 'set' . $name;

        if( 'mapper' == $name || !method_exists($this, $method) )
            throw new Exception("Application_Model_Entry exception - $name is not a valid property!");

        $this->$method($value);
    }

    public function __get($name) {
        $method = 'get' . $name;

        if( 'mapper' == $name || !method_exists($this, $method) )
            throw new Exception("Application_Model_Entry exception - $name is not a valid property!");

        return $this->$method();
    }

    public function setOpts(array $opts) {
        $methods = get_class_methods($this);

        foreach ($opts as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (in_array($method, $methods))
                $this->$method($value);

        }

        return $this;
    }

    public function setId($id) {
        $this->_id = (int) $id;
        return $this;
    }

    public function getId() {
        return $this->_id;
    }

    public function setDescription($description) {
        $this->_description = (string) $description;
        return $this;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function setDrivetime($drivetime) {
        $this->_drivetime = (int) $drivetime;
        return $this;
    }

    public function getDrivetime() {
        return $this->_drivetime;
    }

    public function setMiles($ts) {
        $this->_miles = $ts;
        return $this;
    }

    public function getMiles() {
        return $this->_miles;
    }

    public function setCost($cost) {
        $this->_cost = (float) $cost;
        return $this;
    }

    public function getCost() {
        return $this->_cost;
    }

    public function setMpg($mpg) {
        $this->_mpg = (int) $mpg;
        return $this;
    }

    public function getMpg() {
        return $this->_mpg;
    }

}

