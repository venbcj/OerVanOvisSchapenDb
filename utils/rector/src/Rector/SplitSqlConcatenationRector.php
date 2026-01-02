<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node\Scalar\InterpolatedString;
use PhpParser\BuilderHelpers;
use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Expression;
use Rector\NodeTypeResolver\Node\AttributeKey;
use Rector\RectorDefinition\RectorDefinition;
use Rector\Rector\AbstractRector;

final class SplitSqlConcatenationRector extends AbstractRector {

    use ArgSplitter;

    public function getRuleDefinition(): RuleDefinition {
        return new RuleDefinition(
            'Split SQL string concatenation into placeholders and arguments array',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '" . mysqli_real_escape_string($db, $STALID) . "',
        datum = '" . mysqli_real_escape_string($db, $DATUM) . "',
            actId = '" . mysqli_real_escape_string($db, $ACTID) . "' ";
CODE_SAMPLE
        ,
            <<<'CODE_SAMPLE'
public function insert_tblHistorie($STALID, $DATUM, $ACTID) {
$sql = <<<SQL
INSERT INTO tblHistorie SET stalId = ':param1',
        datum = ':param2',
            actId = ':param3' 
SQL;
$arguments = [[':param1', $STALID], [':param2', $DATUM], [':param3', $ACTID]];
$this->run_query($sql, $arguments);
    }
CODE_SAMPLE
        ),
            ]);
    }

    public function getNodeTypes(): array {
        return [Expression::class];
    }

    public function refactor(Node $node) {
        if (! $node->expr instanceof Assign) {
            return null;
        }
        $assign = $node->expr;
        $query = $assign->expr;
        if (! $this->looksLikeString($query)) {
            return null;
        }
        $var = $assign->var;
        [$sql, $args, $func_args] = $this->splitArgsFromString($query);
        if (! $this->looksLikeQuery($sql)) {
            return null;
        }
        $gateway = $this->guess_gateway_name($sql);
        return [
            $this->composeDeclaration($gateway),
            $this->composeCall($assign, $gateway, $func_args),
            $this->composeMethod($assign, $func_args, $sql, $args),
        ];
    }

}
