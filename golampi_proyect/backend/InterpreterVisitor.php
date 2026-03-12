<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/GolampiParserVisitor.php';
require_once __DIR__ . '/GolampiParserBaseVisitor.php';

class BreakSignal extends Exception {}
class ContinueSignal extends Exception {}

class ReturnSignal extends Exception
{
    public mixed $value;

    public function __construct($value)
    {
        $this->value = $value;
        parent::__construct();
    }
}

class InterpreterVisitor extends GolampiParserBaseVisitor
{
    private array $scopes = [[]];
    private array $output = [];
    private array $symbolTable = [];
    private array $functions = [];
    private array $constants = [];
    private array $scopeLabels = ['global'];
    private array $scopeKinds = ['global'];

    public function getOutput(): array
    {
        return $this->output;
    }

    public function getSymbols(): array
    {
        return $this->symbolTable;
    }
    

    public function visitProgram($context)
{
    foreach ($context->functionDecl() as $func) {
        $name = $func->ID()->getText();
        $this->functions[$name] = $func;

        $this->addSymbolEntry(
    $name,
    'función',
    '-',
    'global',
    $this->getLineFromContext($func),
    $this->getColumnFromContext($func)
);
    }

    if (!isset($this->functions['main'])) {
        throw new Exception("No se encontró la función main.");
    }

    return $this->callUserFunction('main', []);
}

    public function visitFunctionDecl($context)
    {
    return null;
    }

    public function visitBlock($context)
{
    $this->enterScope();

    try {
        foreach ($context->stmt() as $stmt) {
            $this->visit($stmt);
        }
    } finally {
        $this->exitScope();
    }

    return null;
}

    public function visitVarDecl($context)
{
    $ids = $context->idList()->ID();
    $type = $context->typeSpec()->getText();

    $values = [];

    if ($context->exprList() !== null) {
        foreach ($context->exprList()->expr() as $expr) {
            $values[] = $this->visit($expr);
        }

        if (count($ids) !== count($values)) {
            throw new Exception("Cantidad de variables y valores no coincide en declaración larga.");
        }
    }

    for ($i = 0; $i < count($ids); $i++) {
        $id = $ids[$i]->getText();

        if ($context->exprList() !== null) {
            $value = $values[$i];
        } else {
            $value = $this->defaultValueFromType($context->typeSpec());
        }

        $this->declareVariable(
    $id,
    $type,
    $value,
    $this->getLineFromContext($context),
    $this->getColumnFromContext($context)
);
    }

    return null;
}

    public function visitShortDecl($context)
{
    $ids = $context->idList()->ID();
    $exprs = $context->exprList()->expr();

    $values = [];

    foreach ($exprs as $expr) {
        $result = $this->visit($expr);

        if (is_array($result) && !isset($result['_golampi_type']) && array_keys($result) === range(0, count($result) - 1)) {
            foreach ($result as $item) {
                $values[] = $item;
            }
        } else {
            $values[] = $result;
        }
    }

    if (count($ids) !== count($values)) {
        throw new Exception("Cantidad de variables y valores no coincide en declaración corta.");
    }

    for ($i = 0; $i < count($ids); $i++) {
        $id = $ids[$i]->getText();
        $this->declareVariable(
    $id,
    'inferido',
    $values[$i],
    $this->getLineFromContext($context),
    $this->getColumnFromContext($context)
);
    }

    return null;
}

    public function visitAssignStmt($context)
{
    $rootId = $context->lvalue()->ID()->getText();
$resolvedRoot = $this->resolveReferenceName($rootId);

if (isset($this->constants[$resolvedRoot])) {
    throw new Exception("No se puede modificar la constante '$resolvedRoot'.");
}
    $currentValue = $this->getLvalueValue($context->lvalue());
    $newValue = $this->visit($context->expr());
    $op = $context->assignOp()->getText();

    switch ($op) {
        case '=':
            $finalValue = $newValue;
            break;
        case '+=':
            $finalValue = $currentValue + $newValue;
            break;
        case '-=':
            $finalValue = $currentValue - $newValue;
            break;
        case '*=':
            $finalValue = $currentValue * $newValue;
            break;
        case '/=':
            $finalValue = $currentValue / $newValue;
            break;
        default:
            throw new Exception("Operador de asignación no soportado: $op");
    }

    $this->setLvalueValue($context->lvalue(), $finalValue);
    return null;
}

    public function visitIncDecStmt($context)
{
    $currentValue = $this->getLvalueValue($context->lvalue());
    $op = $context->getChild(1)->getText();

    if ($op === '++') {
        $this->setLvalueValue($context->lvalue(), $currentValue + 1);
    } elseif ($op === '--') {
        $this->setLvalueValue($context->lvalue(), $currentValue - 1);
    }

    return null;
}

    public function visitPrintStmt($context)
{
    $values = [];

    if ($context->argList() !== null) {
        foreach ($context->argList()->expr() as $expr) {
            $values[] = $this->visit($expr);
        }
    }

    $formatted = [];
    foreach ($values as $value) {
        $formatted[] = $this->formatValue($value);
    }

    $this->output[] = implode(' ', $formatted);
    return null;
    }

    public function visitExpr($context)
    {
        return $this->visit($context->logicalOr());
    }

    public function visitPrimary($context)
{
    if ($context->INT_LIT()) {
        return (int)$context->INT_LIT()->getText();
    }

    if ($context->FLOAT_LIT()) {
        return (float)$context->FLOAT_LIT()->getText();
    }

    if ($context->STRING_LIT()) {
        return stripcslashes(substr($context->STRING_LIT()->getText(), 1, -1));
    }

    if ($context->RUNE_LIT()) {
        $text = substr($context->RUNE_LIT()->getText(), 1, -1);

        if (strlen($text) === 1) {
            return [
                '_golampi_type' => 'rune',
                '_value' => ord($text)
            ];
        }

        return [
            '_golampi_type' => 'rune',
            '_value' => ord($text[0])
        ];
    }

    if ($context->TRUE()) {
        return true;
    }

    if ($context->FALSE()) {
        return false;
    }

    if ($context->NIL()) {
        return null;
    }

    if ($context->functionCall()) {
        return $this->visit($context->functionCall());
    }

    if ($context->compositeLit()) {
        return $this->visit($context->compositeLit());
    }

    if ($context->lvalue()) {
        return $this->getLvalueValue($context->lvalue());
    }

    if ($context->expr()) {
        return $this->visit($context->expr());
    }

    return null;
}

    public function visitFunctionCall($context)
{
    $name = $context->getChild(0)->getText();
    $args = [];

    if ($context->argList() !== null) {
        foreach ($context->argList()->expr() as $expr) {
            $args[] = $this->visit($expr);
        }
    }

    switch ($name) {
        case 'len':
            if (count($args) !== 1) {
                throw new Exception("len() recibe 1 argumento.");
            }

            if (is_array($args[0])) {
                if (isset($args[0]['_values']) && is_array($args[0]['_values'])) {
                    return count($args[0]['_values']);
                }
                return count($args[0]);
            }

            return strlen((string)$args[0]);

        case 'now':
            return date('Y-m-d H:i:s');

        case 'substr':
            if (count($args) !== 3) {
                throw new Exception("substr() recibe 3 argumentos.");
            }

            return substr((string)$args[0], (int)$args[1], (int)$args[2]);

        case 'typeOf':
            if (count($args) !== 1) {
                throw new Exception("typeOf() recibe 1 argumento.");
            }

            return $this->golampiTypeOf($args[0]);

        default:
            return $this->callUserFunction($name, $args);
    }
}

    public function visitUnary($context)
{
    if ($context->getChildCount() === 2) {
        $op = $context->getChild(0)->getText();

        if ($op === '!') {
            return !$this->visit($context->unary());
        }

        if ($op === '-') {
            return -$this->visit($context->unary());
        }

        if ($op === '&') {
            $inner = $context->unary();

            // Esperamos algo como primary -> lvalue
            if (method_exists($inner, 'primary') && $inner->primary() !== null && $inner->primary()->lvalue() !== null) {
                $lvalue = $inner->primary()->lvalue();
                $rootName = $this->resolveReferenceName($lvalue->ID()->getText());
                return $this->makeReference($rootName);
            }

            throw new Exception("El operador & solo puede aplicarse a variables.");
        }
    }

    return $this->visitChildren($context);
}

    public function visitMultiplicative($context)
    {
        $result = $this->visit($context->unary(0));

        $count = count($context->unary());
        for ($i = 1; $i < $count; $i++) {
            $right = $this->visit($context->unary($i));
            $op = $context->getChild(($i * 2) - 1)->getText();

            switch ($op) {
                case '*':
                    $result *= $right;
                    break;
                case '/':
    if ($right == 0) {
        return null; // o '<nil>' visualmente al imprimir
    }

    if (is_int($result) && is_int($right)) {
        $result = intdiv($result, $right);
    } else {
        $result = $result / $right;
    }
    break;

case '%':
    if ($right == 0) {
        return null;
    }

    $result %= $right;
    break;
            }
        }

        return $result;
    }

    public function visitAdditive($context)
    {
        $result = $this->visit($context->multiplicative(0));

        $count = count($context->multiplicative());
        for ($i = 1; $i < $count; $i++) {
            $right = $this->visit($context->multiplicative($i));
            $op = $context->getChild(($i * 2) - 1)->getText();

            switch ($op) {
                case '+':
                    $result += $right;
                    break;
                case '-':
                    $result -= $right;
                    break;
            }
        }

        return $result;
    }

    public function visitRelational($context)
{
    $result = $this->visit($context->additive(0));

    $count = count($context->additive());
    for ($i = 1; $i < $count; $i++) {
        $right = $this->visit($context->additive($i));
        $op = $context->getChild(($i * 2) - 1)->getText();

        if ($result === null || $right === null) {
            return null;
        }

        switch ($op) {
            case '<':
                $result = $result < $right;
                break;
            case '<=':
                $result = $result <= $right;
                break;
            case '>':
                $result = $result > $right;
                break;
            case '>=':
                $result = $result >= $right;
                break;
        }
    }

    return $result;
}

    public function visitEquality($context)
{
    $result = $this->visit($context->relational(0));

    $count = count($context->relational());
    for ($i = 1; $i < $count; $i++) {
        $right = $this->visit($context->relational($i));
        $op = $context->getChild(($i * 2) - 1)->getText();

        if ($result === null || $right === null) {
            return null;
        }

        switch ($op) {
            case '==':
                $result = $result == $right;
                break;
            case '!=':
                $result = $result != $right;
                break;
        }
    }

    return $result;
}

    public function visitLogicalAnd($context)
{
    $result = $this->visit($context->equality(0));

    $count = count($context->equality());
    for ($i = 1; $i < $count; $i++) {
        if (!$result) {
            return false; // corto circuito
        }

        $right = $this->visit($context->equality($i));
        $result = $result && $right;
    }

    return $result;
}

    public function visitLogicalOr($context)
{
    $result = $this->visit($context->logicalAnd(0));

    $count = count($context->logicalAnd());
    for ($i = 1; $i < $count; $i++) {
        if ($result) {
            return true; // corto circuito
        }

        $right = $this->visit($context->logicalAnd($i));
        $result = $result || $right;
    }

    return $result;
}

    public function visitIfStmt($context)
    {
        $condition = $this->visit($context->expr());

        if ($condition) {
            return $this->visit($context->block(0));
        }

        if ($context->block(1) !== null) {
            return $this->visit($context->block(1));
        }

        return null;
    }

    public function visitForStmt($context)
{
    // for init; cond; post { ... }
    if ($context->forClause() !== null) {
        $forClause = $context->forClause();

        if ($forClause->forInit() !== null) {
            $this->visit($forClause->forInit());
        }

        while (true) {
            if ($forClause->expr() !== null) {
                $condition = $this->visit($forClause->expr());
                if (!$condition) {
                    break;
                }
            }

            try {
                $this->executeLoopBlock($context->block());
            } catch (ContinueSignal $e) {
                // sigue a forPost
            } catch (BreakSignal $e) {
                break;
            }

            if ($forClause->forPost() !== null) {
                $this->visit($forClause->forPost());
            }
        }

        return null;
    }

    // for condicion { ... }
    if ($context->expr() !== null) {
        while ($this->visit($context->expr())) {
            try {
                $this->executeLoopBlock($context->block());
            } catch (ContinueSignal $e) {
                continue;
            } catch (BreakSignal $e) {
                break;
            }
        }
        return null;
    }

    // for { ... }
    while (true) {
        try {
            $this->executeLoopBlock($context->block());
        } catch (ContinueSignal $e) {
            continue;
        } catch (BreakSignal $e) {
            break;
        }
    }

    return null;
}

    public function visitForInit($context)
    {
        return $this->visitChildren($context);
    }

    public function visitForPost($context)
    {
        return $this->visitChildren($context);
    }

    public function visitBreakStmt($context)
{
    throw new BreakSignal();
}

    public function visitContinueStmt($context)
{
    throw new ContinueSignal();
}

public function visitCompositeLit($context)
{
    $type = $context->arrayType()->getText();
    $values = [];

    if ($context->elementList() !== null) {
        foreach ($context->elementList()->element() as $element) {
            $values[] = $this->visit($element);
        }
    }

    return [
        '_golampi_type' => $type,
        '_values' => $values
    ];
}

public function visitElement($context)
{
    if ($context->expr() !== null) {
        return $this->visit($context->expr());
    }

    if ($context->elementList() !== null) {
        $nested = [];
        foreach ($context->elementList()->element() as $element) {
            $nested[] = $this->visit($element);
        }
        return $nested;
    }

    return [];
}

    private function enterScope(): void
{
    $this->enterNamedScope('bloque', 'bloque');
}

private function exitScope(): void
{
    $this->exitNamedScope();
}

    private function declareVariable(
    string $id,
    string $type,
    $value,
    int|string $line = '-',
    int|string $column = '-'
): void {
    $currentIndex = count($this->scopes) - 1;

    if (isset($this->scopes[$currentIndex][$id])) {
        throw new Exception("Variable '$id' ya declarada en este ámbito.");
    }

    $this->scopes[$currentIndex][$id] = [
        'type' => $type,
        'value' => $value
    ];

    $this->addSymbolEntry($id, $type, $value, $this->getCurrentScopeLabel(), $line, $column);
}

    private function assignVariable(string $id, $value): void
{
    for ($i = count($this->scopes) - 1; $i >= 0; $i--) {
        if (isset($this->scopes[$i][$id])) {
            $this->scopes[$i][$id]['value'] = $value;
            $this->updateLastSymbolValue($id, $value);
            return;
        }
    }

    throw new Exception("Variable '$id' no declarada.");
}

    private function getVariable(string $id)
        {
            for ($i = count($this->scopes) - 1; $i >= 0; $i--) {
                if (isset($this->scopes[$i][$id])) {
                    return $this->scopes[$i][$id];
                }
            }

            throw new Exception("Variable '$id' no declarada.");
        }

        private function formatValue($value): string
{
    if ($value === null) {
    return '<nil>';
}

    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_array($value)) {
        if (isset($value['_golampi_type']) && $value['_golampi_type'] === 'rune') {
            return (string)$value['_value'];
        }

        if (isset($value['_values'])) {
            return json_encode($value['_values'], JSON_UNESCAPED_UNICODE);
        }

        return json_encode($value, JSON_UNESCAPED_UNICODE);
    }

    return (string)$value;
}

private function golampiTypeOf($value): string
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
        return 'string';
    }

    if (is_array($value)) {
        if (isset($value['_golampi_type']) && $value['_golampi_type'] === 'rune') {
            return 'rune';
        }

        if (isset($value['_golampi_type'])) {
            return $value['_golampi_type'];
        }

        return 'array';
    }

    return 'unknown';
}

private function callUserFunction(string $name, array $args)
{
    if (!isset($this->functions[$name])) {
        throw new Exception("Función '$name' no declarada.");
    }

    $func = $this->functions[$name];

    $this->enterNamedScope("función $name", 'función');

    // Asignar parámetros
    if ($func->paramList() !== null) {
        $params = $func->paramList()->param();

        if (count($params) !== count($args)) {
            throw new Exception("La función '$name' esperaba " . count($params) . " argumentos y recibió " . count($args) . ".");
        }

        for ($i = 0; $i < count($params); $i++) {
    $paramName = $params[$i]->ID()->getText();

    if ($params[$i]->typeSpec() !== null) {
        $paramType = $params[$i]->typeSpec()->getText();
    } else {
        $paramType = 'inferido';
    }

    $this->declareVariable(
    $paramName,
    $paramType,
    $args[$i],
    $this->getLineFromContext($params[$i]),
    $this->getColumnFromContext($params[$i])
);
}
    } elseif (count($args) > 0) {
        throw new Exception("La función '$name' no recibe argumentos.");
    }

    try {
        $this->visit($func->block());
    } catch (ReturnSignal $r) {
        $this->exitScope();
        return $r->value;
    }

    $this->exitNamedScope();
    return null;
}

public function visitReturnStmt($context)
{
    if ($context->exprList() === null) {
        throw new ReturnSignal(null);
    }

    $values = [];
    foreach ($context->exprList()->expr() as $expr) {
        $values[] = $this->visit($expr);
    }

    if (count($values) === 1) {
        throw new ReturnSignal($values[0]);
    }

    throw new ReturnSignal($values);
}

private function makeReference(string $name): array
{
    return [
        '_ref' => true,
        'name' => $name
    ];
}

private function isReference($value): bool
{
    return is_array($value) && isset($value['_ref']) && $value['_ref'] === true;
}

private function resolveReferenceName(string $id): string
{
    $var = $this->getVariable($id);
    $value = $var['value'];

    if ($this->isReference($value)) {
        return $value['name'];
    }

    return $id;
}

private function getLvalueValue($lvalueContext)
{
    $rootName = $this->resolveReferenceName($lvalueContext->ID()->getText());
    $value = $this->getVariable($rootName)['value'];

    $indexes = [];
    foreach ($lvalueContext->expr() as $expr) {
        $indexes[] = $this->visit($expr);
    }

    if (count($indexes) === 0) {
        return $value;
    }

    foreach ($indexes as $idx) {
        if (is_array($value) && isset($value['_values'])) {
            $value = $value['_values'][$idx];
        } elseif (is_array($value)) {
            $value = $value[$idx];
        } else {
            throw new Exception("Acceso inválido a índice en '$rootName'.");
        }
    }

    return $value;
}

private function setLvalueValue($lvalueContext, $newValue): void
{
    $rootName = $this->resolveReferenceName($lvalueContext->ID()->getText());
    $rootVar = $this->getVariable($rootName);
    $value = $rootVar['value'];

    $indexes = [];
    foreach ($lvalueContext->expr() as $expr) {
        $indexes[] = $this->visit($expr);
    }

    // asignación simple: x = ...
    if (count($indexes) === 0) {
        $this->assignVariable($rootName, $newValue);
        return;
    }

    $copy = $value;

    // Caso arreglo Golampi con _values
    if (isset($copy['_values']) && is_array($copy['_values'])) {
        $ref =& $copy['_values'];

        for ($i = 0; $i < count($indexes) - 1; $i++) {
            $idx = $indexes[$i];
            $ref =& $ref[$idx];

            if (is_array($ref) && isset($ref['_values'])) {
                $ref =& $ref['_values'];
            }
        }

        $last = $indexes[count($indexes) - 1];
        $ref[$last] = $newValue;

        $this->assignVariable($rootName, $copy);
        return;
    }

    // Caso arreglo PHP normal (anidado)
    if (is_array($copy)) {
        $ref =& $copy;

        for ($i = 0; $i < count($indexes) - 1; $i++) {
            $idx = $indexes[$i];
            $ref =& $ref[$idx];
        }

        $last = $indexes[count($indexes) - 1];
        $ref[$last] = $newValue;

        $this->assignVariable($rootName, $copy);
        return;
    }

    throw new Exception("No se puede asignar por índice en '$rootName'.");
}

private function defaultValueFromType($typeCtx)
{
    $text = $typeCtx->getText();

    switch ($text) {
        case 'int32':
            return 0;

        case 'float32':
            return 0.0;

        case 'bool':
            return false;

        case 'string':
            return "";

        case 'rune':
            return [
                '_golampi_type' => 'rune',
                '_value' => 0
            ];
    }

    // Si es arreglo o matriz
    if ($typeCtx->arrayType() !== null) {
        return $this->buildDefaultArray($typeCtx->arrayType());
    }

    // Si es puntero, por ahora nil
    if ($typeCtx->pointerType() !== null) {
        return null;
    }

    return null;
}

private function buildDefaultArray($arrayTypeCtx)
{
    $size = 0;

    if ($arrayTypeCtx->arraySize() !== null) {
        $size = (int)$arrayTypeCtx->arraySize()->getText();
    }

    $elementTypeCtx = $arrayTypeCtx->typeSpec();
    $values = [];

    for ($i = 0; $i < $size; $i++) {
        $values[] = $this->defaultValueFromType($elementTypeCtx);
    }

    return [
        '_golampi_type' => $arrayTypeCtx->getText(),
        '_values' => $values
    ];
}

public function visitConstDecl($context)
{
    $id = $context->ID()->getText();
    $type = $context->typeSpec()->getText();
    $value = $this->visit($context->expr());

    $this->declareVariable(
    $id,
    $type,
    $value,
    $this->getLineFromContext($context),
    $this->getColumnFromContext($context)
);
    $this->constants[$id] = true;

    return null;
}

public function visitSwitchStmt($context)
{
    $switchValue = $this->visit($context->expr());

    // revisa los case
    foreach ($context->caseClause() as $caseClause) {
        $caseValue = $this->visit($caseClause->expr());

        if ($switchValue === null || $caseValue === null) {
            continue;
        }

        if ($switchValue == $caseValue) {
            foreach ($caseClause->stmt() as $stmt) {
                $this->visit($stmt);
            }
            return null;
        }
    }

    // si no coincidió ningún case, ejecuta default
    if ($context->defaultClause() !== null) {
        foreach ($context->defaultClause()->stmt() as $stmt) {
            $this->visit($stmt);
        }
    }

    return null;
}

private function addSymbolEntry(
    string $id,
    string $type,
    $value,
    int|string $scope,
    int|string $line = '-',
    int|string $column = '-'
): void {
    $this->symbolTable[] = [
        'id' => $id,
        'type' => $type,
        'value' => $value,
        'scope' => $scope,
        'line' => $line,
        'column' => $column
    ];
}

private function updateLastSymbolValue(string $id, $value): void
{
    for ($i = count($this->symbolTable) - 1; $i >= 0; $i--) {
        if (($this->symbolTable[$i]['id'] ?? null) === $id) {
            $this->symbolTable[$i]['value'] = $value;
            return;
        }
    }
}

private function getLineFromContext($context): int|string
{
    if (isset($context->start) && method_exists($context->start, 'getLine')) {
        return $context->start->getLine();
    }
    return '-';
}

private function getColumnFromContext($context): int|string
{
    if (isset($context->start) && method_exists($context->start, 'getCharPositionInLine')) {
        return $context->start->getCharPositionInLine() + 1;
    }
    return '-';
}


private function getCurrentScopeLabel(): string
{
    return $this->scopeLabels[count($this->scopeLabels) - 1] ?? 'global';
}

private function enterNamedScope(string $label, string $kind): void
{
    $this->scopes[] = [];
    $this->scopeLabels[] = $label;
    $this->scopeKinds[] = $kind;
}

private function exitNamedScope(): void
{
    array_pop($this->scopes);
    array_pop($this->scopeLabels);
    array_pop($this->scopeKinds);
}


private function executeLoopBlock($blockContext): void
{
    $this->enterNamedScope('ciclo for', 'ciclo');
    try {
        foreach ($blockContext->stmt() as $stmt) {
            $this->visit($stmt);
        }
    } finally {
        $this->exitNamedScope();
    }
}

}
