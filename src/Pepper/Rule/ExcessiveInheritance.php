<?php

namespace Pepper\Rule;

class ExcessiveInheritance extends ClassRule
{
    public function enterNode(\PHPParser_Node $node)
    {
        parent::enterNode($node);

        if (!($node instanceof \PHPParser_Node_Stmt_Class)) {
            return;
        }

        $class = $this->currentClass;
        $num = 0;

        while (($parent = $this->project->getParent($class)) !== null) {
            $class = $parent;
            $num++;
        }

        if ($num >= 2) {
            $this->addMessage($node);
        }
    }

}
