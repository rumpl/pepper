<?php

namespace Pepper\Node;

class PepperClass extends Node
{
    /**
     * @var $_node \PHPParser_Node_Stmt_Class
     */
    private $_node;
    private $_namespace;
    private $_uses;

    public function __construct($namespace, $node, $uses)
    {
        $this->_namespace = $namespace;
        $this->_node = $node;
        $this->_uses = $uses;
    }

    public function fullName()
    {
        return $this->_namespace . '\\' . $this->_node->name;
    }

    public function parentFullName()
    {
        $extends = $this->_node->extends;

        if ($extends instanceof \PHPParser_Node_Name_FullyQualified) {
            return $extends->parts[0];
        }

        if ($extends === null) {
            return null;
        }

        $parentClassName = $extends->parts[0];

        return $this->_findFullyQualifiedClassName($parentClassName);
    }

    private function _findFullyQualifiedClassName($className)
    {
        foreach($this->_uses as $use) {
            /** @var $use \PHPParser_Node_Stmt_Use */
            foreach($use->uses as $u) {
                /** @var $use \PHPParser_Node_Stmt_UseUSe */
                if ($u->alias === $className) {
                    return implode('\\', $u->name->parts);
                }
            }
        }

        return $this->_namespace . '\\' . $className;
    }
}
