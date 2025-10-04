<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use PhpParser\Node\Name;
use PhpParser\Node\Expr\BinaryOp\LogicalOr;
use PhpParser\Node\Expr\Exit_;

/**
 * @see \Rector\Tests\TypeDeclaration\Rector\MysqliQueryFunctionCallToDbMethodCallRector\MysqliQueryFunctionCallToDbMethodCallRectorTest
 */
final class RemoveOrDieConstructRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('// @todo fill the description', [
            new CodeSample(
                <<<'CODE_SAMPLE'
mysqli_query($db, "SELECT 1 FROM table") or die (mysql_error($db));
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$this->db->query("SELECT 1 FROM table");
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [\PhpParser\Node\Expr\BinaryOp\LogicalOr::class];
        # return [\PhpParser\Node\Expr\BinaryOp_LogicalOr::class];
    }

    /**
     * @param \PhpParser\Node\Stmt\Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        # if (!$node->right instanceof Exit_) {
        #     return null;
        # }
        return $node->left;
    }
}
