<?php

require_once 'PepperTestCase.php';

class NestingTooDeepTest extends PepperTestCase
{
    public function setUp()
    {
        $this->code = '
<?php
if (true) {
    if (false) {
    }
}';
        $this->setUpPepper(
            array(
                'Pepper\Rule\NestingTooDeep' => array(
                    'level' => 'warning',
                    'params' => array(
                        'threshold' => 2
                    )
                )
            )
        );
    }

    public function testShouldFindNesting()
    {
        $messages = $this->getPepperMessages();

        $this->assertEquals(1, count($messages));
    }

    public function testShouldFindTheRightStatement()
    {
        $messages = $this->getPepperMessages();

        $message = $messages[0];

        $this->assertEquals(4, $message->node->getLine());
    }
}
