<?php

namespace Pepper\Rule;

use Pepper\PepperRule;
use Pepper\Project;
use Pepper\Report\Report;
use Pepper\Node\PepperClass;

class ClassRule extends PepperRule
{
    /**
     * @var $project Project
     */
    protected $project;

    /**
     * @var $_namespace string
     */
    private $_namespace;

    /**
     * @var $currentClass PepperClass
     */
    protected $currentClass;

    public function __construct(Report $report, Project $project)
    {
        parent::__construct($report);
        $this->project = $project;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            /** @var $node \PHPParser_Node_Stmt_Namespace */
            $this->_namespace = $node->name;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Class) {
            /** @var $node \PHPParser_Node_Stmt_Class */
            $this->currentClass = $this->project->getClass($this->_namespace . '\\' . $node->name);
        }
    }
}
