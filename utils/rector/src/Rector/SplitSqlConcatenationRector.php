<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\String_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Rector\Tests\TypeDeclaration\Rector\SplitSqlConcatenationRector\SplitSqlConcatenationRectorTest
 */
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

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Expression::class];
    }

    /**
     * @param \PhpParser\Node\Stmt\Class_ $node
     */
    public function refactor(Node $node)
    {
        if (! $node->expr instanceof Assign) {
            return null;
        }
        $node = $node->expr;

        if (! $node->expr instanceof Concat) {
            return null;
        }

        // Flatten concatenation
        $parts = $this->flattenConcat($node->expr);

        // Must start with string
        if (! $parts || ! $parts[0] instanceof String_) {
            return null;
        }

        $sql = '';
        $arguments = [];
        $paramIndex = 1;

        foreach ($parts as $part) {
            if ($part instanceof String_) {
                $sql .= $part->value;
            } else {
                $param = ':param' . $paramIndex++;
                $sql .= $param;
                $arguments[] = [$param, $part];
            }
        }

        // Replace assignment: $sql = "..."
        $node->expr = new String_($sql);

        // Insert arguments array after assignment
        $items = [];

        foreach ($arguments as $arg) {
            // $arg = [':param1', Expr $valueExpr]

            $innerItems = [
                new ArrayItem(new String_($arg[0])),
                new ArrayItem($arg[1]),
            ];

            $items[] = new ArrayItem(new Array_($innerItems));
        }

        $argumentsAssign = new Assign(
            new Variable('arguments'),
            new Array_($items)
        );

        return [new Expression($node), new Expression($argumentsAssign)];
    }

    /**
     * @return Expr[]
     */
    private function flattenConcat(Expr $expr): array
    {
        if ($expr instanceof Concat) {
            return array_merge(
                $this->flattenConcat($expr->left),
                $this->flattenConcat($expr->right)
            );
        }

        return [$expr];
    }
}

