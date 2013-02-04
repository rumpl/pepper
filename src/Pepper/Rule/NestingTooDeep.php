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

use Pepper\PepperRule;
use Pepper\Report\Report;

class NestingTooDeep extends PepperRule
{

    private $threshold;

    private $_nestingLevel;

    public function __construct(Report $report, $threshold)
    {
        parent::__construct($report);
        $this->threshold = $threshold;
    }

    public function beforeTraverse(array $nodes)
    {
        $this->_nestingLevel = 0;
    }

    public function enterNode(\PHPParser_Node $node)
    {
        if (!$this->_isNesting($node)) {
            return;
        }

        $this->_nestingLevel++;

        if ($this->_nestingLevel >= $this->threshold) {
            $this->addMessage($node);
        }
    }

    public function leaveNode(\PHPParser_Node $node)
    {
        if ($this->_isNesting($node)) {
            $this->_nestingLevel--;
        }
    }

    private function _isNesting($node)
    {
        return $node instanceof \PHPParser_Node_Stmt_If ||
          $node instanceof \PHPParser_Node_Stmt_TryCatch ||
          $node instanceof \PHPParser_Node_Stmt_For ||
          $node instanceof \PHPParser_Node_Stmt_Foreach ||
          $node instanceof \PHPParser_Node_Stmt_While;
    }
}
