<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/GolampiLexer.php';
require_once __DIR__ . '/GolampiParser.php';
require_once __DIR__ . '/InterpreterVisitor.php';

use Antlr\Antlr4\Runtime\InputStream;
use Antlr\Antlr4\Runtime\CommonTokenStream;

try {
    $input = InputStream::fromPath(__DIR__ . '/test.golampi');

    $lexer = new GolampiLexer($input);
    $tokens = new CommonTokenStream($lexer);

    $parser = new GolampiParser($tokens);
    $tree = $parser->program();

    echo "PARSE OK\n";

    $visitor = new InterpreterVisitor();
    $visitor->visit($tree);

    echo "\n=== SALIDA ===\n";
    foreach ($visitor->getOutput() as $line) {
        echo $line . PHP_EOL;
    }

    echo "\n=== TABLA DE SIMBOLOS ===\n";
    print_r($visitor->getSymbols());

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}