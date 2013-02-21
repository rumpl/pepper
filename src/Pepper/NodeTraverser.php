<?php

namespace Pepper;

class NodeTraverser extends \PHPParser_NodeTraverser
{
    public function traverse(array $nodes)
    {
        foreach ($this->visitors as $visitor) {
            if (null !== $return = $visitor->beforeTraverse($nodes)) {
                $nodes = $return;
            }
        }

        while (!empty($nodes)) {
            /** @var $node \PHPParser_Node */
            $node = array_shift($nodes);

            if (is_array($node)) {
                if (count($node) === 0) continue;
                $n = array_shift($node);
                array_unshift($nodes, $node);
                $node = $n;
            }

            if (!$node instanceof \PHPParser_Node) {
                continue;
            }

            if ($node->getAttribute('entered')) {
                foreach ($this->visitors as $visitor) {
                    $visitor->leaveNode($node);
                }
                continue;
            }

            foreach ($this->visitors as $visitor) {
                $visitor->enterNode($node);
            }
            $node->setAttribute('entered', true);

            array_unshift($nodes, $node);

            foreach ($node->getSubNodeNames() as $name) {
                array_unshift($nodes, $node->$name);
            }

        }

        foreach ($this->visitors as $visitor) {
            if (null !== $return = $visitor->afterTraverse($nodes)) {
                $nodes = $return;
            }
        }
    }
}
