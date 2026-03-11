<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/GolampiLexer.php';

use Antlr\Antlr4\Runtime\InputStream;
use Antlr\Antlr4\Runtime\CommonTokenStream;

$path = __DIR__ . '/test.golampi';
if (!file_exists($path)) {
    fwrite(STDERR, "No existe el archivo: $path\n");
    exit(1);
}

$input = InputStream::fromPath($path);

$lexer = new GolampiLexer($input);
$tokens = new CommonTokenStream($lexer);
$tokens->fill();

$invalidType = GolampiLexer::INVALID_CHAR;

// En este runtime no hay size(). Usamos cantidad de tokens en canal normal.
$n = $tokens->getNumberOfOnChannelTokens();
$all = $tokens->getTokens(0, $n - 1);

foreach ($all as $t) {
    $type = $t->getType();
    $text = $t->getText();
    $line = $t->getLine();
    $col  = $t->getCharPositionInLine();

    if ($type === -1) { // EOF
        echo "[$line:$col] EOF\n";
        continue;
    }

    $tokenName = $lexer->getVocabulary()->getSymbolicName($type) ?? (string)$type;

    if ($type === $invalidType) {
        echo "[$line:$col] LEXICAL_ERROR $tokenName text=" . json_encode($text) . "\n";
    } else {
        echo "[$line:$col] $tokenName text=" . json_encode($text) . "\n";
    }
}