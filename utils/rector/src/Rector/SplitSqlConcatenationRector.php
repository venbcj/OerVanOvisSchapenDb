<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\BinaryOp\Concat;
use PhpParser\Node\Scalar\String_;
use Rector\Core\Rector\AbstractRector;
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
        return [Assign::class];
    }

    /**
     * @param \PhpParser\Node\Stmt\Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof Assign) {
            return null;
        }

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
        $argumentsAssign = new Assign(
            new Expr\Variable('arguments'),
            $this->nodeFactory->createArray(
                array_map(
                    fn ($arg) => [
                        $this->nodeFactory->createArrayItem(
                            $arg[1],
                            null,
                            false,
                            [
                                $this->nodeFactory->createArrayItem(
                                    new String_($arg[0])
                                ),
                            ]
                        )
                    ],
                    $arguments
                )
            )
        );

        return [$node, $argumentsAssign];
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

