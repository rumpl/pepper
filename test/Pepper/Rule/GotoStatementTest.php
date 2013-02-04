<?php

require_once 'PepperTestCase.php';

class GotoStatementTest extends PepperTestCase
{
    public function setUp()
    {
        $this->code = '<?php
lbl:
    print "hello";
goto lbl;';

        $this->setUpPepper(
            array(
                'Pepper\Rule\GotoStatement' =>
                array(
                    'level' => 'warning'
                )
            )
        );
    }

    public function testShouldFindGotoStatement()
    {
        $messages = $this->getPepperMessages();

        $this->assertEquals(1, count($messages));
    }

    public function testShouldReturnTheRightNode()
    {
        $messages = $this->getPepperMessages();

        $this->assertTrue(
            $messages[0]->node instanceof PHPParser_Node_Stmt_Goto
        );
    }
}
