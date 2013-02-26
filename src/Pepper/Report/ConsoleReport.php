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

namespace Pepper\Report;

use Pepper\Report\Report;
use Pepper\Report\ReportMessage;

class ConsoleReport implements Report
{
    /**
     * @var $messages array
     */
    private $messages;
    /**
     * @var $color \Colors\Color
     */
    private $color;
    /**
     * @var $configuration array
     */
    private $configuration;
    /**
     * @var $pepperConfiguration array
     */
    private $pepperConfiguration;

    public function __construct($configuration, $pepperConfiguration, $color)
    {
        $this->configuration = $configuration;
        $this->pepperConfiguration = $pepperConfiguration;
        $this->color = $color;

        $this->messages = array();
    }

    public function addMessage(ReportMessage $message)
    {
        $this->messages[] = $message;
    }

    public function dump()
    {
        $file = '';
        foreach ($this->messages as $message) {
            $messageFile = $message->node->getAttribute('fileName');
            if ($messageFile !== $file) {
                print PHP_EOL . $messageFile . PHP_EOL;
                $file = $messageFile;
            }

            $this->dumpMessage($message);
        }
    }

    // TODO : rewrite interpolation.
    private function interpolate(ReportMessage $message)
    {
        $metadata = $message->getMetadata();
        $ruleClass = get_class($message->rule);
        $mess = $this->configuration[$ruleClass];

        foreach ($metadata as $key => $value) {
            $mess = str_replace('{' . $key . '}', $value, $mess);
        }

        return $mess;
    }

    private function dumpMessage(ReportMessage $message)
    {
        $prefix = '•';

        $level = $this->getLevel($message);
        if ($level === 'error') {
            $prefix = '✖';
        }
        if ($level === 'warning') {
            $prefix = '⚠';
        }

        $mess = "    " . $prefix . ' ' . $this->interpolate($message);

        print $this->color->fg($this->getColor($message), $mess) . PHP_EOL;
    }

    private function getColor(ReportMessage $message)
    {
        $level = $this->getLevel($message);

        if ($level === 'notice') {
            return 'white';
        }

        if ($level === 'warning') {
            return 'yellow';
        }

        if ($level === 'error') {
            return 'red';
        }

        return 'white';
    }

    private function getLevel($message)
    {
        $ruleClass = get_class($message->rule);
        $ruleConfiguration = $this->pepperConfiguration[$ruleClass];
        return $ruleConfiguration['level'];
    }
}
