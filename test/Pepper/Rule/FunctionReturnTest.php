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

class FunctionReturnTest extends PepperTestCase
{
    public function setUp()
    {
        $this->code = '<?php 1 == 1;';

        $this->setUpPepper(
            array(
                'Pepper\Rule\FunctionReturn' =>
                array(
                    'level' => 'warning'
                )
            )
        );
    }

    public function testShoudFindMissingReturnStatementAfterIf()
    {
        $this->code = '<?php
        function test() {
            if (false) {
                return 0;
            }
            print 1;
        }';

        $messages = $this->getPepperMessages();
        $this->assertEquals(1, count($messages));
    }

    public function testShouldFindMissingReturnStatementComplex()
    {
        $this->code = '<?php
        function test() {
            if (true) {
                if (true) {
                    return 1;
                } else {
                    print "test";
                }
            }
        }
        ';

        $messages = $this->getPepperMessages();
        $this->assertEquals(1, count($messages));
    }

    public function testShouldFindMissingReturnInElse()
    {
        $this->code = '<?php
        function test() {
            if (true) {
                return 1;
            } else {
                print "test";
            }
        }';

        $messages = $this->getPepperMessages();
        $this->assertEquals(1, count($messages));
    }

    public function testShouldFindAllReturns()
    {
        $this->code = '<?php
        function test() {
            if (true) {
                return 1;
            } else {
                return 0;
            }
        }';

        $messages = $this->getPepperMessages();
        $this->assertEquals(0, count($messages));
    }

    public function testShouldFindReturnInWhileLoop()
    {
        $this->code = '<?php
        function test() {
            while(true) {
                if (false) {
                    return 1;
                }
            }
        }';

        $messages = $this->getPepperMessages();
        $this->assertEquals(1, count($messages));
    }
}
