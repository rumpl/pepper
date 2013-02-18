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

use PHPParser_NodeTraverser;
use Pepper\Metric\ClassMetric;
use DirectoryIterator;
use ReflectionClass;
use PHPParser_Lexer;
use PHPParser_Parser;

class Pepper
{
    private $report;

    private $configuration;

    public function __construct($report, $configuration)
    {
        $this->report = $report;
        $this->configuration = $configuration;
    }

    /**
     * Analyzes a file and returns the report.
     *
     * @param $file string
     * @return \Pepper\Report\Report
     */
    public function analyzeFile($file)
    {
        return $this->analyzeCode(file_get_contents($file));
    }

    private function addVisitor(PHPParser_NodeTraverser $traverse, $ruleName, $ruleConfiguration)
    {
        $arguments = array('report' => $this->report);

        if (isset($ruleConfiguration['params'])) {
            foreach ($ruleConfiguration['params'] as $key => $param) {
                $arguments[$key] = $param;
            }
        }

        $refClass = new ReflectionClass($ruleName);
        $classInstance = $refClass->newInstanceArgs($arguments);
        $traverse->addVisitor($classInstance);
    }

    /**
     * Analyzes a string containing PHP code and returns the report.
     *
     * @param $code string
     * @return \Pepper\Report\Report
     */
    public function analyzeCode($code)
    {
        $traverse = new PHPParser_NodeTraverser;

        $parser = new PHPParser_Parser(new PHPParser_Lexer);

        $statements = $parser->parse($code);

        foreach ($this->configuration as $ruleName => $ruleConfiguration) {
            $this->addVisitor($traverse, $ruleName, $ruleConfiguration);
        }

        $traverse->traverse($statements);

        return $this->report;
    }

    public function analyzeDirectory($dir)
    {
        $parser = new PHPParser_Parser(new PHPParser_Lexer);

        $project = new Project();

        $classMetric = new ClassMetric($project);

        $traverse = new PHPParser_NodeTraverser;
        $traverse->addVisitor($classMetric);

        $dirs = array($dir);
        while (count($dirs) !== 0) {
            $dir = array_pop($dirs);

            foreach (new DirectoryIterator($dir) as $fileInfo) {
                /** @var $fileInfo DirectoryIterator */
                $file_extension = pathinfo($fileInfo->getFilename(), PATHINFO_EXTENSION);
                if ($fileInfo->isFile()) {
                    if ($file_extension === 'php') {
                        $statements = $parser->parse(file_get_contents($fileInfo->getPathname()));
                        $traverse->traverse($statements);
                    }
                }

                if ($fileInfo->isDir() && !$fileInfo->isDot()) {
                    $dirs[] = $fileInfo->getPathname();
                }
            }
        }

        return $project;
    }
}
