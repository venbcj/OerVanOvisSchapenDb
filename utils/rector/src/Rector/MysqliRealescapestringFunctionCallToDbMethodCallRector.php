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
 * @see \Rector\Tests\TypeDeclaration\Rector\MysqliRealescapestringFunctionCallToDbMethodCallRector\MysqliRealescapestringFunctionCallToDbMethodCallRectorTest
 */
final class MysqliRealescapestringFunctionCallToDbMethodCallRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('// @todo fill the description', [
            new CodeSample(
                <<<'CODE_SAMPLE'
mysqli_real_escape_string($db, $lidId);
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$this->db->real_escape_string($lidId);
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
        if ($this->getName($node->name) != 'mysqli_real_escape_string') {
            return null;
        }
        $newnode = new MethodCall(
            new PropertyFetch(
                new Variable(
                    'this'
                ),
                new Identifier('db')
            ),
            new Identifier('real_escape_string'),
            [$node->args[1]]
        );

        return $newnode;
    }
}
