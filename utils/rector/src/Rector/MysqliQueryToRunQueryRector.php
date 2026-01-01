<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Arg;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class MysqliQueryToRunQueryRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'change mysqli_query into this->run_query(sql, arguments)',
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
        return [FuncCall::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (! $node instanceof FuncCall) {
            return null;
        }

        $func_call = $node;

        if (! $this->isName($func_call->name, 'mysqli_query')) {
            return null;
        }

        if (count($func_call->args) < 2) {
            return null;
        }

        $node = new MethodCall(
            new Variable('this'),
            'run_query',
            [
                $func_call->args[1],
                new Arg(new Variable('arguments')),
            ]
        );

        return $node;
    }
}

