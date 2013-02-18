<?php

namespace Pepper\Metric;

use Pepper\Node\PepperClass;
use Pepper\Project;

class ClassMetric extends \PHPParser_NodeVisitorAbstract
{
    /**
     * @var $_project Project;
     */
    private $_project;

    private $_namespace;

    private $_uses;

    public function __construct(Project $project)
    {
        $this->_project = $project;
        $this->_uses = array();
    }

    public function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Use) {
            $this->_uses []= $node;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            /** @var $node \PHPParser_Node_Stmt_Namespace */
            $this->_namespace = $node->name;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Class) {
            /** @var $node \PHPParser_Node_Stmt_Class */
            $this->_project->addClass(new PepperClass($this->_namespace, $node, $this->_uses));
        }
    }
}
