parser grammar GolampiParser;

options { tokenVocab=GolampiLexer; }

program
    : functionDecl+ EOF
    ;

functionDecl
    : FUNC ID LPAREN paramList? RPAREN returnType? block
    ;

paramList
    : param (COMMA param)*
    ;

param
    : ID typeSpec
    ;

returnType
    : typeSpec
    | LPAREN typeList RPAREN
    ;

typeList
    : typeSpec (COMMA typeSpec)*
    ;

block
    : LBRACE stmt* RBRACE
    ;

stmt
    : varDecl
    | shortDecl
    | assignStmt
    | incDecStmt
    | printStmt
    | ifStmt
    | forStmt
    | breakStmt
    | switchStmt
    | continueStmt
    | returnStmt
    | exprStmt
    | block
    | constDecl
    ;

varDecl
    : VAR idList typeSpec (ASSIGN exprList)?
    ;

constDecl
    : CONST ID typeSpec ASSIGN expr
    ;

shortDecl
    : idList SHORT_DECL exprList
    ;

assignStmt
    : lvalue assignOp expr
    ;

assignOp
    : ASSIGN
    | ADD_ASSIGN
    | SUB_ASSIGN
    | MUL_ASSIGN
    | DIV_ASSIGN
    ;

printStmt
    : PRINT LPAREN argList? RPAREN
    ;

ifStmt
    : IF expr block (ELSE block)?
    ;

switchStmt
    : SWITCH expr LBRACE caseClause* defaultClause? RBRACE
    ;

caseClause
    : CASE expr COLON stmt*
    ;

defaultClause
    : DEFAULT COLON stmt*
    ;

forStmt
    : FOR expr block
    | FOR forClause block
    | FOR block
    ;

forClause
    : forInit? SEMI expr? SEMI forPost?
    ;

forInit
    : varDecl
    | shortDecl
    | assignStmt
    | exprStmt
    ;

forPost
    : incDecStmt
    | assignStmt
    | exprStmt
    ;

incDecStmt
    : lvalue INCR
    | lvalue DECR
    ;

breakStmt
    : BREAK
    ;

continueStmt
    : CONTINUE
    ;

returnStmt
    : RETURN exprList?
    ;

exprStmt
    : expr
    ;

argList
    : expr (COMMA expr)*
    ;

exprList
    : expr (COMMA expr)*
    ;

idList
    : ID (COMMA ID)*
    ;

typeSpec
    : INT32
    | FLOAT32
    | BOOL
    | RUNE
    | STRING
    | arrayType
    | pointerType
    ;

pointerType
    : MUL typeSpec
    ;

arrayType
    : LBRACK arraySize? RBRACK typeSpec
    ;

arraySize
    : INT_LIT
    ;

lvalue
    : ID (LBRACK expr RBRACK)*
    ;

expr
    : logicalOr
    ;

logicalOr
    : logicalAnd (OR logicalAnd)*
    ;

logicalAnd
    : equality (AND equality)*
    ;

equality
    : relational ((EQ | NEQ) relational)*
    ;

relational
    : additive ((LT | LEQ | GT | GEQ) additive)*
    ;

additive
    : multiplicative ((ADD | SUB) multiplicative)*
    ;

multiplicative
    : unary ((MUL | DIV | MOD) unary)*
    ;

unary
    : NOT unary
    | SUB unary
    | AMP unary
    | primary
    ;

primary
    : INT_LIT
    | FLOAT_LIT
    | STRING_LIT
    | RUNE_LIT
    | TRUE
    | FALSE
    | NIL
    | functionCall
    | compositeLit
    | lvalue
    | LPAREN expr RPAREN
    ;

functionCall
    : (ID | LEN | NOW | SUBSTR | TYPEOF) LPAREN argList? RPAREN
    ;

compositeLit
    : arrayType LBRACE elementList? RBRACE
    ;

elementList
    : element (COMMA element)* COMMA?
    ;

element
    : expr
    | LBRACE elementList? RBRACE
    ;