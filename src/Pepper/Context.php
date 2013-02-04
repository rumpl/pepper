<?php
/** Pepper
 *
 * The MIT License (MIT)
 * Copyright © 2013 Djordje Lukic, http://rumpl.org
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the “Software”), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Pepper;

class Context
{
    private $_definedVariables = array();
    private $_variables = array();

    /**
     * @var $_parentContext Context
     */
    private $_parentContext;

    public $variables;

    public function __construct($parentContext)
    {
        $this->_parentContext = $parentContext;
        $this->variables = array();
    }

    public function collect(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Function) {
            /** @var $node \PHPParser_Node_Stmt_Function */
            foreach ($node->params as $param) {
                /** @var $param \PHPParser_Node_Param */
                $this->_definedVariables[] = $param;
            }
        }

        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            /** @var $node \PHPParser_Node_Stmt_ClassMethod */
            foreach ($node->params as $param) {
                /** @var $param \PHPParser_Node_Param */
                $this->_definedVariables[] = $param;
            }
        }

        if ($node instanceof \PHPParser_Node_Expr_Assign) {
            /** @var $node \PHPParser_Node_Expr_Assign */
            $this->_definedVariables[] = $node->var;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Foreach) {
            /** @var $node \PHPParser_Node_Stmt_Foreach */
            if ($node->keyVar !== null) {
                $this->_definedVariables[] = $node->keyVar;
            }
            $this->_definedVariables[] = $node->valueVar;
        }

        if ($node instanceof\ PHPParser_Node_Stmt_Catch) {
            /** @var $node \PHPParser_Node_Stmt_Catch */
            $this->_definedVariables[] = new \PHPParser_Node_Expr_Variable($node->var, $node->getAttributes(
            ));
        }

        if ($node instanceof \PHPParser_Node_Expr_Variable) {
            $this->_variables[] = $node;
        }

    }

    public function dump()
    {
        return $this->_findUninitializedVariables();
    }

    // TODO : refactor
    private function _findUninitializedVariables()
    {
        $vars = array();
        foreach ($this->_variables as $usedVariable) {
            if ($usedVariable->name === 'this') {
                continue;
            }

            $found = false;

            foreach ($this->_definedVariables as $definedVariable) {
                if ($usedVariable->name === $definedVariable->name) {
                    $found = true;
                }
            }

            if (!$found) {
                $vars[] = $usedVariable;
            }
        }
        return $vars;
    }

    public function parentContext()
    {
        return $this->_parentContext === null ? $this : $this->_parentContext;
    }

}
