<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', '0');

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

function formatSymbolValue(mixed $value): string
{
    if ($value === null) {
        return '-';
    }

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_scalar($value)) {
        return (string)$value;
    }

    if (is_array($value)) {
        // Caso referencia estilo: { "_ref": true, "name": "numeroBase" }
        if (isset($value['_ref']) && $value['_ref'] === true && isset($value['name'])) {
            return '&' . $value['name'];
        }

        // Caso arreglo interno estilo:
        // { "_golampi_type": "[4]int32", "_values": [60,75,82,90] }
        if (isset($value['_values']) && is_array($value['_values'])) {
            return '[' . implode(', ', array_map('formatSymbolValue', $value['_values'])) . ']';
        }

        // Arreglo normal
        $formatted = array_map('formatSymbolValue', $value);
        return '[' . implode(', ', $formatted) . ']';
    }

    if (is_object($value)) {
        $vars = get_object_vars($value);

        // Si el objeto tiene forma de referencia
        if (isset($vars['_ref']) && $vars['_ref'] === true && isset($vars['name'])) {
            return '&' . $vars['name'];
        }

        // Si el objeto tiene forma de arreglo Golampi
        if (isset($vars['_values']) && is_array($vars['_values'])) {
            return '[' . implode(', ', array_map('formatSymbolValue', $vars['_values'])) . ']';
        }

        return json_encode($vars, JSON_UNESCAPED_UNICODE);
    }

    return (string)$value;
}

function normalizeSymbols(array $symbols): array
{
    $normalized = [];

    foreach ($symbols as $symbol) {
        if (!is_array($symbol)) {
            continue;
        }

        $normalized[] = [
            'id'    => $symbol['id'] ?? $symbol['name'] ?? '-',
            'type'  => $symbol['type'] ?? '-',
            'value' => formatSymbolValue($symbol['value'] ?? null),
            'scope' => $symbol['scope'] ?? 'global',
            'line'  => $symbol['line'] ?? '-',
        ];
    }

    return $normalized;
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonResponse([
            'success' => false,
            'errorType' => 'HTTP',
            'errors' => ['Método no permitido. Usa POST.'],
            'message' => 'Método no permitido. Usa POST.'
        ], 405);
    }

    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);

    if (!is_array($data)) {
        $data = $_POST;
    }

    if (!is_array($data) || !isset($data['code'])) {
        jsonResponse([
            'success' => false,
            'errorType' => 'REQUEST',
            'errors' => ['No se recibió el código fuente.'],
            'message' => 'No se recibió el código fuente.'
        ], 400);
    }

    $code = trim((string)$data['code']);

    if ($code === '') {
        jsonResponse([
            'success' => false,
            'errorType' => 'REQUEST',
            'errors' => ['El editor está vacío.'],
            'message' => 'El editor está vacío.'
        ], 400);
    }

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
        $tree = $parser->program();

        $visitor = new InterpreterVisitor();
        $visitor->visit($tree);

        $symbols = normalizeSymbols($visitor->getSymbols());

        jsonResponse([
            'success' => true,
            'output' => $visitor->getOutput(),
            'symbols' => $symbols,
            'errors' => [],
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
        'errors' => [$e->getMessage()],
        'message' => $e->getMessage()
    ], 500);
}