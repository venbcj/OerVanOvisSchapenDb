<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Name;
use PhpParser\Node\Arg;
use PhpParser\BuilderHelpers;
use Rector\Rector\AbstractRector;
use Rector\RectorDefinition\RectorDefinition;
use Rector\NodeTypeResolver\Node\AttributeKey;
use PhpParser\Node\Scalar\String_;

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
        if (! $assign->expr instanceof Concat) {
            return null;
        }
        [$sql, $args] = $this->splitArgsFromString($assign->expr);
        return [
            new Expression(new Assign(
                $assign->var,
                new String_($sql, [
                    AttributeKey::KIND => String_::KIND_HEREDOC,
                    AttributeKey::DOC_LABEL => 'SQL',
                ])
            )),
            new Expression(new Assign(
                new Variable('arguments'),
                $this->nodeFactory->createArray($args)
            )),
        ];
    }

}
