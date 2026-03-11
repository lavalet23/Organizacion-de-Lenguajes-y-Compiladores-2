<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/GolampiLexer.php';
require_once __DIR__ . '/GolampiParser.php';
require_once __DIR__ . '/GolampiParserVisitor.php';
require_once __DIR__ . '/GolampiParserBaseVisitor.php';
require_once __DIR__ . '/InterpreterVisitor.php';

use Antlr\Antlr4\Runtime\InputStream;
use Antlr\Antlr4\Runtime\CommonTokenStream;

function jsonResponse(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            'success' => false,
            'errorType' => 'HTTP',
            'message' => 'Método no permitido. Usa POST.'
        ], 405);
    }

    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!is_array($data) || !isset($data['code'])) {
        jsonResponse([
            'success' => false,
            'errorType' => 'REQUEST',
            'message' => 'No se recibió el código fuente.'
        ], 400);
    }

    $code = trim((string)$data['code']);

    if ($code === '') {
        jsonResponse([
            'success' => false,
            'errorType' => 'REQUEST',
            'message' => 'El editor está vacío.'
        ], 400);
    }

    // Crear archivo temporal para el InputStream
    $tmpFile = tempnam(sys_get_temp_dir(), 'golampi_');
    if ($tmpFile === false) {
        throw new Exception('No se pudo crear el archivo temporal.');
    }

    file_put_contents($tmpFile, $code);

    try {
        $input = InputStream::fromPath($tmpFile);

        $lexer = new GolampiLexer($input);
        $tokens = new CommonTokenStream($lexer);

        $parser = new GolampiParser($tokens);

        // Parseo principal
        $tree = $parser->program();

        $visitor = new InterpreterVisitor();
        $visitor->visit($tree);

        jsonResponse([
            'success' => true,
            'output' => $visitor->getOutput(),
            'symbols' => $visitor->getSymbols(),
            'message' => 'Código ejecutado correctamente.'
        ]);
    } finally {
        if (file_exists($tmpFile)) {
            unlink($tmpFile);
        }
    }

} catch (Throwable $e) {
    jsonResponse([
        'success' => false,
        'errorType' => 'EXECUTION',
        'message' => $e->getMessage()
    ], 500);
}