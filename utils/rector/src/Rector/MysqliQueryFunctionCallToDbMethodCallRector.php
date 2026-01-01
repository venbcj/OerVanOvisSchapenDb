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
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Expr\Variable;

/**
 * @see \Rector\Tests\TypeDeclaration\Rector\MysqliQueryFunctionCallToDbMethodCallRector\MysqliQueryFunctionCallToDbMethodCallRectorTest
 */
final class MysqliQueryFunctionCallToDbMethodCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('// @todo fill the description', [
            new CodeSample(
                <<<'CODE_SAMPLE'
mysqli_query($db, "SELECT 1 FROM table");
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
        return [FuncCall::class];
    }

    /**
     * @param \PhpParser\Node\Stmt\Class_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if ($this->getName($node->name) != 'mysqli_query') {
            return null;
        }
        // build: $this->run_query($sql)
        $newnode = new MethodCall(
            new Variable(
                'this'
            ),
            new Identifier('run_query'),
            [$node->args[1]]
        );

        return $newnode;
                // Build: $gateway->query($sql)
        return new MethodCall(
            new Expr\Variable('gateway'),
            'query',
            [$this->nodeFactory->createArg($sqlExpr)]
        );
    }
}
