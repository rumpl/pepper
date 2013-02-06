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

require_once 'PepperTestCase.php';

class UnnecessaryElseTest extends PepperTestCase
{
    public function setUp()
    {
        $this->setUpPepper(
            array(
                'Pepper\Rule\UnnecessaryElse' => array(
                    'level' => 'warning'
                )
            )
        );
    }

    public function testShouldFindUnnecessaryElse()
    {
        $this->code = '<?php if (true) { return 1; } else { return 2; };';
        $messages = $this->getPepperMessages();
        $this->assertEquals(1, count($messages));
    }

    public function testShouldFindComplexReturns()
    {
        $this->code = '<?php if (true) { if (2) return 1; return 2; } else { return 2; };';
        $messages = $this->getPepperMessages();
        $this->assertEquals(1, count($messages));
    }

    public function testShouldFindTheRightElse()
    {
        $this->code = '<?php if (true) {
                if (2) {
                    return 1;
                }
                return 2;
              } else {
                return 2;
              };';
        $messages = $this->getPepperMessages();
        $this->assertEquals(6, $messages[0]->node->getLine());
    }
}
