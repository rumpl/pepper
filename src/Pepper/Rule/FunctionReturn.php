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

namespace Pepper\Rule;

use \Pepper\PepperRule;

class FunctionReturn extends PepperRule
{
    private $atLeastOneReturns;
    private $pathReturns;
    private $current;
    private $isReachable;

    public function beforeTraverse(array $nodes)
    {
        $this->current = 0;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Function || $node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $this->isReachable = true;

            $this->current = 0;
            $this->pathReturns = array(false);

            $this->atLeastOneReturns = false;

            return;
        }

        if ($this->isBranch($node)) {
            $this->current++;
            $this->pathReturns[$this->current] = false;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Return) {
            $this->atLeastOneReturns = true;
            $this->pathReturns[$this->current] = true;
        }
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_If && isset($node->else)) {
            $this->isReachable = false;
        }

        if ($this->isBranch($node)) {
            $this->current--;

            if (!$this->isReachable) {
                $this->pathReturns[$this->current] = true;
            }

            $this->isReachable = true;
        }

        if (!($node instanceof \PHPParser_Node_Stmt_Function || $node instanceof \PHPParser_Node_Stmt_ClassMethod)) {
            return;
        }

        if (!$this->atLeastOneReturns) {
            return;
        }

        foreach ($this->pathReturns as $pathReturn) {
            if (!$pathReturn) {
                $this->addMessage($node);
                return;
            }
        }
    }

    private function isBranch($stmt)
    {
        return $stmt instanceof \PHPParser_Node_Stmt_If ||
            $stmt instanceof \PHPParser_Node_Stmt_Else ||
            $stmt instanceof \PHPParser_Node_Stmt_ElseIf ||
            $stmt instanceof \PHPParser_Node_Stmt_For ||
            $stmt instanceof \PHPParser_Node_Stmt_Foreach ||
            $stmt instanceof \PHPParser_Node_Stmt_While;
    }
}
