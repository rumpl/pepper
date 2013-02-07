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
    public function enterNode(\PHPParser_Node $node)
    {
        if (!($node instanceof \PHPParser_Node_Stmt_Function || $node instanceof \PHPParser_Node_Stmt_ClassMethod)) {
            return;
        }

        $returns = false;

        $numStmts = count($node->stmts);

        for ($i = 0; $i < $numStmts; $i++) {
            $stmt = $node->stmts[$i];
//            var_dump($stmt);
            $returns |= $this->statementReturns($stmt);

            if ($i === $numStmts - 1) {
                break;
            }
        }

        if ($returns && !($node->stmts[$numStmts - 1] instanceof \PHPParser_Node_Stmt_Return)) {
            $this->addMessage($node);
        }
    }

    private function statementReturns(\PHPParser_Node $stmt)
    {
        if ($stmt instanceof \PHPParser_Node_Stmt_Return) {
            return true;
        }

        $returns = false;
        if ($stmt instanceof \PHPParser_Node_Stmt_If ||
            $stmt instanceof \PHPParser_Node_Stmt_Else ||
            $stmt instanceof \PHPParser_Node_Stmt_ElseIf ||
            $stmt instanceof \PHPParser_Node_Stmt_For ||
            $stmt instanceof \PHPParser_Node_Stmt_Foreach ||
            $stmt instanceof \PHPParser_Node_Stmt_While
        ) {
            foreach ($stmt->stmts as $s) {
                $returns |= $this->statementReturns($s);
            }

            if (isset($stmt->elseifs)) {
                foreach ($stmt->elseifs as $ei) {
                    foreach($ei->stmts as $s) {
                        $returns |= $this->statementReturns($s);
                    }
                }
            }

            if (isset($stmt->else)) {
                foreach ($stmt->else->stmts as $s) {
                    $returns |= $this->statementReturns($s);
                }
            }

            return $returns;
        }

        return false;
    }
}
