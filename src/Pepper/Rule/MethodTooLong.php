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

class MethodTooLong extends PepperRule
{
    private $threshold;

    public function __construct($report, $threshold)
    {
        parent::__construct($report);
        $this->threshold = $threshold;
    }
    public function enterNode(\PHPParser_Node $node)
    {
        if (!($node instanceof \PHPParser_Node_Stmt_ClassMethod)) {
            return;
        }

        /** @var $node \PHPParser_Node_Stmt_ClassMethod */
        if (count($node->stmts) === 0) {
            return;
        }

        $endLine = $node->getAttribute('endLine');
        $startLine = $node->getAttribute('startLine');

        $lineNumber = $endLine - $startLine;

        if ($lineNumber > $this->threshold) {
            $this->addMessage($node);
        }
    }
}
