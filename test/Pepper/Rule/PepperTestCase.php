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

use Pepper\Pepper;
use Pepper\Report\DummyReport;

abstract class PepperTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var $pepper \Pepper\Pepper
     */
    protected $pepper;

    /**
     * @var $code string
     */
    protected $code;

    /**
     * Setup the pepper instance with a Dummy report
     *
     * @param $configuration array
     */
    public function setUpPepper($configuration)
    {
        $this->pepper = new Pepper(new DummyReport, $configuration);
    }

    protected function getPepperMessages()
    {
        $report = $this->pepper->analyzeCode($this->code);

        return $report->dump();
    }
}
