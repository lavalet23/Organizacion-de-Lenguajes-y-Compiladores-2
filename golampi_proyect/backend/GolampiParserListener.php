<?php

/*
 * Generated from GolampiParser.g4 by ANTLR 4.13.1
 */

use Antlr\Antlr4\Runtime\Tree\ParseTreeListener;

/**
 * This interface defines a complete listener for a parse tree produced by
 * {@see GolampiParser}.
 */
interface GolampiParserListener extends ParseTreeListener {
	/**
	 * Enter a parse tree produced by {@see GolampiParser::program()}.
	 * @param $context The parse tree.
	 */
	public function enterProgram(Context\ProgramContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::program()}.
	 * @param $context The parse tree.
	 */
	public function exitProgram(Context\ProgramContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::functionDecl()}.
	 * @param $context The parse tree.
	 */
	public function enterFunctionDecl(Context\FunctionDeclContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::functionDecl()}.
	 * @param $context The parse tree.
	 */
	public function exitFunctionDecl(Context\FunctionDeclContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::paramList()}.
	 * @param $context The parse tree.
	 */
	public function enterParamList(Context\ParamListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::paramList()}.
	 * @param $context The parse tree.
	 */
	public function exitParamList(Context\ParamListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::param()}.
	 * @param $context The parse tree.
	 */
	public function enterParam(Context\ParamContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::param()}.
	 * @param $context The parse tree.
	 */
	public function exitParam(Context\ParamContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::returnType()}.
	 * @param $context The parse tree.
	 */
	public function enterReturnType(Context\ReturnTypeContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::returnType()}.
	 * @param $context The parse tree.
	 */
	public function exitReturnType(Context\ReturnTypeContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::typeList()}.
	 * @param $context The parse tree.
	 */
	public function enterTypeList(Context\TypeListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::typeList()}.
	 * @param $context The parse tree.
	 */
	public function exitTypeList(Context\TypeListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::block()}.
	 * @param $context The parse tree.
	 */
	public function enterBlock(Context\BlockContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::block()}.
	 * @param $context The parse tree.
	 */
	public function exitBlock(Context\BlockContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::stmt()}.
	 * @param $context The parse tree.
	 */
	public function enterStmt(Context\StmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::stmt()}.
	 * @param $context The parse tree.
	 */
	public function exitStmt(Context\StmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::varDecl()}.
	 * @param $context The parse tree.
	 */
	public function enterVarDecl(Context\VarDeclContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::varDecl()}.
	 * @param $context The parse tree.
	 */
	public function exitVarDecl(Context\VarDeclContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::constDecl()}.
	 * @param $context The parse tree.
	 */
	public function enterConstDecl(Context\ConstDeclContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::constDecl()}.
	 * @param $context The parse tree.
	 */
	public function exitConstDecl(Context\ConstDeclContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::shortDecl()}.
	 * @param $context The parse tree.
	 */
	public function enterShortDecl(Context\ShortDeclContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::shortDecl()}.
	 * @param $context The parse tree.
	 */
	public function exitShortDecl(Context\ShortDeclContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::assignStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterAssignStmt(Context\AssignStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::assignStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitAssignStmt(Context\AssignStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::assignOp()}.
	 * @param $context The parse tree.
	 */
	public function enterAssignOp(Context\AssignOpContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::assignOp()}.
	 * @param $context The parse tree.
	 */
	public function exitAssignOp(Context\AssignOpContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::printStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterPrintStmt(Context\PrintStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::printStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitPrintStmt(Context\PrintStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::ifStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterIfStmt(Context\IfStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::ifStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitIfStmt(Context\IfStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::switchStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterSwitchStmt(Context\SwitchStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::switchStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitSwitchStmt(Context\SwitchStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::caseClause()}.
	 * @param $context The parse tree.
	 */
	public function enterCaseClause(Context\CaseClauseContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::caseClause()}.
	 * @param $context The parse tree.
	 */
	public function exitCaseClause(Context\CaseClauseContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::defaultClause()}.
	 * @param $context The parse tree.
	 */
	public function enterDefaultClause(Context\DefaultClauseContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::defaultClause()}.
	 * @param $context The parse tree.
	 */
	public function exitDefaultClause(Context\DefaultClauseContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::forStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterForStmt(Context\ForStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::forStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitForStmt(Context\ForStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::forClause()}.
	 * @param $context The parse tree.
	 */
	public function enterForClause(Context\ForClauseContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::forClause()}.
	 * @param $context The parse tree.
	 */
	public function exitForClause(Context\ForClauseContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::forInit()}.
	 * @param $context The parse tree.
	 */
	public function enterForInit(Context\ForInitContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::forInit()}.
	 * @param $context The parse tree.
	 */
	public function exitForInit(Context\ForInitContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::forPost()}.
	 * @param $context The parse tree.
	 */
	public function enterForPost(Context\ForPostContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::forPost()}.
	 * @param $context The parse tree.
	 */
	public function exitForPost(Context\ForPostContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::incDecStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterIncDecStmt(Context\IncDecStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::incDecStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitIncDecStmt(Context\IncDecStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::breakStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterBreakStmt(Context\BreakStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::breakStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitBreakStmt(Context\BreakStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::continueStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterContinueStmt(Context\ContinueStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::continueStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitContinueStmt(Context\ContinueStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::returnStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterReturnStmt(Context\ReturnStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::returnStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitReturnStmt(Context\ReturnStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::exprStmt()}.
	 * @param $context The parse tree.
	 */
	public function enterExprStmt(Context\ExprStmtContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::exprStmt()}.
	 * @param $context The parse tree.
	 */
	public function exitExprStmt(Context\ExprStmtContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::argList()}.
	 * @param $context The parse tree.
	 */
	public function enterArgList(Context\ArgListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::argList()}.
	 * @param $context The parse tree.
	 */
	public function exitArgList(Context\ArgListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::exprList()}.
	 * @param $context The parse tree.
	 */
	public function enterExprList(Context\ExprListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::exprList()}.
	 * @param $context The parse tree.
	 */
	public function exitExprList(Context\ExprListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::idList()}.
	 * @param $context The parse tree.
	 */
	public function enterIdList(Context\IdListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::idList()}.
	 * @param $context The parse tree.
	 */
	public function exitIdList(Context\IdListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::typeSpec()}.
	 * @param $context The parse tree.
	 */
	public function enterTypeSpec(Context\TypeSpecContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::typeSpec()}.
	 * @param $context The parse tree.
	 */
	public function exitTypeSpec(Context\TypeSpecContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::pointerType()}.
	 * @param $context The parse tree.
	 */
	public function enterPointerType(Context\PointerTypeContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::pointerType()}.
	 * @param $context The parse tree.
	 */
	public function exitPointerType(Context\PointerTypeContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::arrayType()}.
	 * @param $context The parse tree.
	 */
	public function enterArrayType(Context\ArrayTypeContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::arrayType()}.
	 * @param $context The parse tree.
	 */
	public function exitArrayType(Context\ArrayTypeContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::arraySize()}.
	 * @param $context The parse tree.
	 */
	public function enterArraySize(Context\ArraySizeContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::arraySize()}.
	 * @param $context The parse tree.
	 */
	public function exitArraySize(Context\ArraySizeContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::lvalue()}.
	 * @param $context The parse tree.
	 */
	public function enterLvalue(Context\LvalueContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::lvalue()}.
	 * @param $context The parse tree.
	 */
	public function exitLvalue(Context\LvalueContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::expr()}.
	 * @param $context The parse tree.
	 */
	public function enterExpr(Context\ExprContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::expr()}.
	 * @param $context The parse tree.
	 */
	public function exitExpr(Context\ExprContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::logicalOr()}.
	 * @param $context The parse tree.
	 */
	public function enterLogicalOr(Context\LogicalOrContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::logicalOr()}.
	 * @param $context The parse tree.
	 */
	public function exitLogicalOr(Context\LogicalOrContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::logicalAnd()}.
	 * @param $context The parse tree.
	 */
	public function enterLogicalAnd(Context\LogicalAndContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::logicalAnd()}.
	 * @param $context The parse tree.
	 */
	public function exitLogicalAnd(Context\LogicalAndContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::equality()}.
	 * @param $context The parse tree.
	 */
	public function enterEquality(Context\EqualityContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::equality()}.
	 * @param $context The parse tree.
	 */
	public function exitEquality(Context\EqualityContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::relational()}.
	 * @param $context The parse tree.
	 */
	public function enterRelational(Context\RelationalContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::relational()}.
	 * @param $context The parse tree.
	 */
	public function exitRelational(Context\RelationalContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::additive()}.
	 * @param $context The parse tree.
	 */
	public function enterAdditive(Context\AdditiveContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::additive()}.
	 * @param $context The parse tree.
	 */
	public function exitAdditive(Context\AdditiveContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::multiplicative()}.
	 * @param $context The parse tree.
	 */
	public function enterMultiplicative(Context\MultiplicativeContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::multiplicative()}.
	 * @param $context The parse tree.
	 */
	public function exitMultiplicative(Context\MultiplicativeContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::unary()}.
	 * @param $context The parse tree.
	 */
	public function enterUnary(Context\UnaryContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::unary()}.
	 * @param $context The parse tree.
	 */
	public function exitUnary(Context\UnaryContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::primary()}.
	 * @param $context The parse tree.
	 */
	public function enterPrimary(Context\PrimaryContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::primary()}.
	 * @param $context The parse tree.
	 */
	public function exitPrimary(Context\PrimaryContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::functionCall()}.
	 * @param $context The parse tree.
	 */
	public function enterFunctionCall(Context\FunctionCallContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::functionCall()}.
	 * @param $context The parse tree.
	 */
	public function exitFunctionCall(Context\FunctionCallContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::compositeLit()}.
	 * @param $context The parse tree.
	 */
	public function enterCompositeLit(Context\CompositeLitContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::compositeLit()}.
	 * @param $context The parse tree.
	 */
	public function exitCompositeLit(Context\CompositeLitContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::elementList()}.
	 * @param $context The parse tree.
	 */
	public function enterElementList(Context\ElementListContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::elementList()}.
	 * @param $context The parse tree.
	 */
	public function exitElementList(Context\ElementListContext $context): void;
	/**
	 * Enter a parse tree produced by {@see GolampiParser::element()}.
	 * @param $context The parse tree.
	 */
	public function enterElement(Context\ElementContext $context): void;
	/**
	 * Exit a parse tree produced by {@see GolampiParser::element()}.
	 * @param $context The parse tree.
	 */
	public function exitElement(Context\ElementContext $context): void;
}