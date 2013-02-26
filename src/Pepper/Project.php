<?php

namespace Pepper;

use Pepper\Node\PepperClass;

class Project
{
    private $_classes;

    public function __construct()
    {
        $this->_classes = array();
    }

    public function addClass(PepperClass $class)
    {
        $this->_classes[$class->fullName()] = $class;
    }

    /**
     * @param $fullName
     * @return null|PepperClass
     */
    public function getClass($fullName)
    {
        return isset($this->_classes[$fullName]) ? $this->_classes[$fullName] : null;
    }

    /**
     * @param $class PepperClass
     * @return null|PepperClass
     */
    public function getParent(PepperClass $class)
    {
        $parent = $class->parentFullName();
        return isset($this->_classes[$parent]) ? $this->_classes[$parent] : null;
    }

    public function dump()
    {
        foreach ($this->_classes as $class) {
            /** @var $class PepperClass */
            print $class->fullName();
            while (($parent = $this->getParent($class)) !== null) {
                print ' extends ' . $parent->fullName();
                $class = $parent;
            }

            print PHP_EOL;
        }
    }
}
