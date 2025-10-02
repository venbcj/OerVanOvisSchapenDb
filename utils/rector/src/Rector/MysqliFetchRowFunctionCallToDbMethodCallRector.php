<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use PhpParser\Node\Name;
use PhpParser\Node\Identifier;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;

/**
 * @see \Rector\Tests\TypeDeclaration\Rector\MysqliQueryFunctionCallToDbMethodCallRector\MysqliQueryFunctionCallToDbMethodCallRectorTest
 */
final class MysqliFetchRowFunctionCallToDbMethodCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('// @todo fill the description', [
            new CodeSample(
                <<<'CODE_SAMPLE'
$row = mysqli_fetch_row($result);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$row = $result->fetch_row();
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [\PhpParser\Node\Expr\FuncCall::class];
    }

    /**
     * @param \PhpParser\Node\Stmt\Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->getName($node->name) != 'mysqli_fetch_row') {
            return null;
        }
        $newnode = new MethodCall(
            $node->args[0]->value,
            new Identifier('fetch_row')
        );

        return $newnode;
    }
}
