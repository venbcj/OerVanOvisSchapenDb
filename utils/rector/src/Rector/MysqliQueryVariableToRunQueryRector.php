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

final class MysqliQueryVariableToRunQueryRector extends AbstractRector {

    public function getRuleDefinition(): RuleDefinition {
        return new RuleDefinition(
            'change mysqli_query(db, statement) into this->run_query(sql, arguments)',
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

    public function getNodeTypes(): array {
        return [FuncCall::class];
    }

    public function refactor(Node $node): ?Node {
        if (! $node instanceof FuncCall) {
            return null;
        }
        if (! $this->isName($node->name, 'mysqli_query')) {
            return null;
        }
        if (count($node->args) < 2) {
            return null;
        }
        // deze regel behandelt het geval met de query in een variabele
        if (! $node->args[1]->value instanceof Variable) {
            return null;
        }
        return new MethodCall(
            new Variable('this'),
            'run_query',
            [
                $node->args[1],
                new Arg(new Variable('arguments')),
            ]
        );
    }

}
