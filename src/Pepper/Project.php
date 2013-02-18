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
        $this->_classes [$class->fullName()] = $class;
    }

    public function dump()
    {
        foreach ($this->_classes as $fullName => $class) {
            /** @var $class PepperClass */
            $parent = $class->parentFullName();

            print $fullName . ($parent ? ' extends ' . $parent : '');

            if (isset($this->_classes[$parent])) {
                $grandParent =$this->_classes[$parent]->parentFullName();
                print ($grandParent ? ' extends ' . $grandParent : '') . PHP_EOL;
            } else {
                print PHP_EOL;
            }
        }
    }
}
