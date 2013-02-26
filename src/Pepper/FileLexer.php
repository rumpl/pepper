<?php

namespace Pepper;

use InvalidArgumentException;

class FileLexer extends \PHPParser_Lexer
{
    protected $fileName;

    public function startLexing($fileName)
    {
        if (!file_exists($fileName)) {
            throw new InvalidArgumentException(sprintf('File "%s" does not exist', $fileName));
        }

        $this->fileName = $fileName;
        parent::startLexing(file_get_contents($fileName));
    }

    public function getNextToken(&$value = null, &$startAttributes = null, &$endAttributes = null)
    {
        $tokenId = parent::getNextToken($value, $startAttributes, $endAttributes);

        // we could use either $startAttributes or $endAttributes here, because the fileName is always the same
        // (regardless of whether it is the start or end token). We choose $endAttributes, because it is slightly
        // more efficient (as the parser has to keep a stack for the $startAttributes).
        $endAttributes['fileName'] = $this->fileName;

        return $tokenId;
    }
}
