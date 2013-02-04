<?php

require '../vendor/autoload.php';

use Pepper\Report\ConsoleReport;
use Pepper\Pepper;
use Symfony\Component\Yaml\Yaml;
use Colors\Color;
use Symfony\Component\DependencyInjection\ContainerBuilder;

// TODO : $code = '<?php $$toto = "a";';
// TODO : Unreachable code.
// TODO : Toutes les branches de code ne retournent pas.

$code = '<?php

class Test {
  public function test() {
    print "asdf";
    print "asdf";
    print "asdf";
    print "asdf";
  }
  public function m1() {}
  public function m2() {}
  public function m3() {}
  public function m4() {}
  public function m5() {}
}

function ifif() {
  if(true) {
    print "asdf";
    print "asdf";
  }
}

function t() {
    $t = "";
}

function r() {
    global $tata;
    $t .= "";
}

$$toto = "asdf";
$toto = "asdf";
$tata .= "asdf";

$toto == $toto;

$toto === $toto;

if (true) {
    return true;
} else {
    return false;
}

if (true) {
    return 1;
}

if (true) {
    if(true) {
        if(true) {
        }
    }
}
';

class PHPBuilderInterface extends \PHPParser_BuilderAbstract
{
    public $name;
    protected $methods;

    public function __construct($name)
    {
        $this->name = $name;
        $this->methods = array();
    }

    public function addStmt($stmt)
    {
        $stmt = $this->normalizeNode($stmt);

        $targets = array('Stmt_ClassMethod' => &$this->methods);

        $type = $stmt->getType();
        if (!isset($targets[$type])) {
            throw new LogicException(sprintf(
                'Unexpected node of type "%s"',
                $type
            ));
        }

        $targets[$type][] = $stmt;

        return $this;
    }

    public function getNode()
    {
        return new PHPParser_Node_Stmt_Interface($this->name, array('stmts' => $this->methods));
    }
}

class PepperBuilderFactory extends PHPParser_BuilderFactory
{
    public function buildInterface($name)
    {
        return new PHPBuilderInterface($name);
    }
}

// Test avec un peu d'injection de dependances.
$container = new ContainerBuilder();
$container->register('factory', 'PepperBuilderFactory');

//$factory = new PepperBuilderFactory;

/** @var $factory PepperBuilderFactory */
$factory = $container->get('factory');

$node = $factory->buildInterface('Test')
  ->addStmt(
    $factory
      ->method('test')
      ->addParam(new PHPParser_Builder_Param('toto'))
)
  ->addStmt($factory->method('toto'))
  ->getNode();

$pp = new PHPParser_PrettyPrinter_Default();
$interfaceStatements = array($node);
//print $pp->prettyPrint($interfaceStatements) . PHP_EOL;

$ar = Yaml::parse('config.yaml');
$par = Yaml::parse('pepperconfig.yaml');

$pepper = new Pepper(new ConsoleReport($ar, $par, new Color), $par);

$report = $pepper->analyzeCode($code);

$report->dump();
