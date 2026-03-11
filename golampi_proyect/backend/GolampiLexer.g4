lexer grammar GolampiLexer;


// Palabras reservadas

IF      : 'if' ;
ELSE    : 'else' ;
FOR     : 'for' ;
FUNC    : 'func' ;
RETURN  : 'return' ;
VAR     : 'var' ;
CONST   : 'const' ;
BREAK   : 'break' ;
CONTINUE: 'continue' ;

TRUE    : 'true' ;
FALSE   : 'false' ;
NIL     : 'nil' ;

PRINT   : 'fmt.Println' ;

SWITCH  : 'switch' ;
CASE    : 'case' ;
DEFAULT : 'default' ;

// Built-in functions

LEN     : 'len' ;
NOW     : 'now' ;
SUBSTR  : 'substr' ;
TYPEOF  : 'typeOf' ;


// Tipos de datos

INT32   : 'int32' ;
FLOAT32 : 'float32' ;
BOOL    : 'bool' ;
RUNE    : 'rune' ;
STRING  : 'string' ;


// Operadores compuestos

SHORT_DECL : ':=' ;

ADD_ASSIGN : '+=' ;
SUB_ASSIGN : '-=' ;
MUL_ASSIGN : '*=' ;
DIV_ASSIGN : '/=' ;


// Operadores básicos

INCR    : '++' ;
DECR    : '--' ;

EQ      : '==' ;
NEQ     : '!=' ;
LEQ     : '<=' ;
GEQ     : '>=' ;

ADD     : '+' ;
SUB     : '-' ;
MUL     : '*' ;
DIV     : '/' ;
MOD     : '%' ;
ASSIGN  : '=' ;

LT      : '<' ;
GT      : '>' ;

AND     : '&&' ;
OR      : '||' ;
NOT     : '!' ;
AMP     : '&' ;


// Delimitadores

LBRACE  : '{' ;
RBRACE  : '}' ;
LPAREN  : '(' ;
RPAREN  : ')' ;
LBRACK  : '[' ;
RBRACK  : ']' ;
COMMA   : ',' ;
SEMI    : ';' ;
COLON   : ':' ;
DOT     : '.' ;


// Literales

// rune literal: 'a', '\n', '\t'
RUNE_LIT
    : '\'' ( '\\' . | ~['\\\r\n] ) '\''
    ;

// string literal
STRING_LIT
    : '"' ( ~["\\] | '\\' . )* '"'
    ;

// números
FLOAT_LIT
    : [0-9]+ '.' [0-9]+
    ;

INT_LIT
    : [0-9]+
    ;

// Identificadores

ID
    : [a-zA-Z_][a-zA-Z_0-9]*
    ;

// Espacios y comentarios

WS
    : [ \t\r\n]+ -> skip
    ;

COMMENT
    : '//' ~[\r\n]* -> skip
    ;

BLOCK_COMMENT
    : '/*' .*? '*/' -> skip
    ;


// Error léxico

INVALID_CHAR
    : .
    ;