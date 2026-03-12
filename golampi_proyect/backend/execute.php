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

function parseAntlrErrors(string $text): array
{
    $errors = [];
    $lines = preg_split('/\r\n|\r|\n/', trim($text));

    foreach ($lines as $lineText) {
        $lineText = trim($lineText);

        if ($lineText === '') {
            continue;
        }

        // Formato típico de ANTLR:
        // line 2:4 token recognition error at: '@'
        // line 3:0 mismatched input '}' expecting ...
        if (preg_match('/^line\s+(\d+):(\d+)\s+(.*)$/i', $lineText, $matches)) {
            $line = (int)$matches[1];
            $column = (int)$matches[2] + 1;
            $message = trim($matches[3]);

            $type = str_contains(strtolower($message), 'token recognition error')
                ? 'Léxico'
                : 'Sintáctico';

            $errors[] = [
                'type' => $type,
                'message' => $message,
                'line' => $line,
                'column' => $column,
            ];
        } else {
            $errors[] = [
                'type' => 'Sintáctico',
                'message' => $lineText,
                'line' => '-',
                'column' => '-',
            ];
        }
    }

    return $errors;
}

function inferTypeFromValue(mixed $value): string
{
    if ($value === null) {
        return 'nil';
    }

    if (is_bool($value)) {
        return 'bool';
    }

    if (is_int($value)) {
        return 'int32';
    }

    if (is_float($value)) {
        return 'float32';
    }

    if (is_string($value)) {
        // Si quisieras distinguir rune/string habría que hacerlo desde el intérprete.
        return 'string';
    }

    if (is_array($value)) {
        // Referencia tipo { "_ref": true, "name": "numeroBase" }
        if (isset($value['_ref']) && $value['_ref'] === true) {
            return 'referencia';
        }

        // Arreglo interno tipo { "_golampi_type": "[4]int32", "_values": [...] }
        if (isset($value['_golampi_type'])) {
            return (string)$value['_golampi_type'];
        }

        if (isset($value['_values']) && is_array($value['_values'])) {
            $count = count($value['_values']);
            $innerType = 'inferido';

            if ($count > 0) {
                $innerType = inferTypeFromValue($value['_values'][0]);
            }

            return '[' . $count . ']' . $innerType;
        }

        return 'array';
    }

    if (is_object($value)) {
        $vars = get_object_vars($value);

        if (isset($vars['_ref']) && $vars['_ref'] === true) {
            return 'referencia';
        }

        if (isset($vars['_golampi_type'])) {
            return (string)$vars['_golampi_type'];
        }

        if (isset($vars['_values']) && is_array($vars['_values'])) {
            $count = count($vars['_values']);
            $innerType = 'inferido';

            if ($count > 0) {
                $innerType = inferTypeFromValue($vars['_values'][0]);
            }

            return '[' . $count . ']' . $innerType;
        }

        return 'objeto';
    }

    return 'desconocido';
}

function normalizeSymbolType(mixed $type, mixed $value): string
{
    $type = $type ?? '-';

    if ($type === 'inferido') {
        return inferTypeFromValue($value) . ' (inferido)';
    }

    return (string)$type;
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

        $formatted = array_map('formatSymbolValue', $value);
        return '[' . implode(', ', $formatted) . ']';
    }

    if (is_object($value)) {
        $vars = get_object_vars($value);

        if (isset($vars['_ref']) && $vars['_ref'] === true && isset($vars['name'])) {
            return '&' . $vars['name'];
        }

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

        $rawValue = $symbol['value'] ?? null;

        $normalized[] = [
    'id'     => $symbol['id'] ?? $symbol['name'] ?? '-',
    'type'   => normalizeSymbolType($symbol['type'] ?? '-', $rawValue),
    'value'  => formatSymbolValue($rawValue),
    'scope'  => $symbol['scope'] ?? 'global',
    'line'   => $symbol['line'] ?? '-',
    'column' => $symbol['column'] ?? '-',
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

ob_start();

$lexer = new GolampiLexer($input);
$tokens = new CommonTokenStream($lexer);

$parser = new GolampiParser($tokens);
$tree = $parser->program();

// Detectar errores sintácticos de ANTLR
if ($parser->getNumberOfSyntaxErrors() > 0) {
    $line = '-';
    $column = '-';

    if (method_exists($parser, 'getCurrentToken')) {
        $currentToken = $parser->getCurrentToken();

        if ($currentToken !== null) {
            if (method_exists($currentToken, 'getLine')) {
                $line = $currentToken->getLine();
            }
            if (method_exists($currentToken, 'getCharPositionInLine')) {
                $column = $currentToken->getCharPositionInLine() + 1;
            }
        }
    }

    jsonResponse([
        'success' => false,
        'errorType' => 'SINTACTICO',
        'output' => [],
        'symbols' => [],
        'errors' => [[
            'type' => 'Sintáctico',
            'message' => 'Se encontraron errores sintácticos en el código.',
            'line' => $line,
            'column' => $column
        ]],
        'message' => 'Se encontraron errores sintácticos en el código.'
    ], 400);
}

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
    $msg = $e->getMessage();
    $type = 'Semántico';

    if (stripos($msg, 'léxico') !== false || stripos($msg, 'lexico') !== false) {
        $type = 'Léxico';
    } elseif (stripos($msg, 'sintáctico') !== false || stripos($msg, 'sintactico') !== false) {
        $type = 'Sintáctico';
    }

    jsonResponse([
        'success' => false,
        'errorType' => strtoupper($type),
        'output' => [],
        'symbols' => [],
        'errors' => [[
            'type' => $type,
            'message' => $msg,
            'line' => '-',
            'column' => '-'
        ]],
        'message' => $msg
    ], 400);
}