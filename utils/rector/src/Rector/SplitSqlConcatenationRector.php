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

final class SplitSqlConcatenationRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Split SQL string concatenation into placeholders and arguments array',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
// @todo fill code before
CODE_SAMPLE
        ,
            <<<'CODE_SAMPLE'
// @todo fill code after
CODE_SAMPLE
        ),
            ]);
    }
    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    public function refactor(Node $node)
    {
        if (! $node->expr instanceof Assign) {
            return null;
        }

        $assign = $node->expr;
        if (! $assign->expr instanceof Concat) {
            return null;
        }

        $parts = $this->flattenConcat($assign->expr);

        $sql = '';
        $args = [];
        $i = 1;

        foreach ($parts as $part) {
            if ($part instanceof String_) {
                $sql .= $part->value;
            } else {
                $param = ':param' . $i++;
                $sql .= $param;
                $args[] = [$param, $part];
            }
        }

        return [
            new Expression(new Assign(
                $assign->var,
                new String_($sql)
            )),
            new Expression(new Assign(
                new Variable('arguments'),
                $this->nodeFactory->createArray($args)
            )),
        ];
    }

    private function flattenConcat(Concat $concat): array
    {
        return array_merge(
            $concat->left instanceof Concat ? $this->flattenConcat($concat->left) : [$concat->left],
            $concat->right instanceof Concat ? $this->flattenConcat($concat->right) : [$concat->right]
        );
    }
}
