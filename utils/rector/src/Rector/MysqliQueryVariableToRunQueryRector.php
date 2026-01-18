<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Expression;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Arg;
use PhpParser\NodeTraverser;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;

final class MysqliQueryVariableToRunQueryRector extends AbstractRector {

    public function getRuleDefinition(): RuleDefinition {
        return new RuleDefinition(
            'change mysqli_query(db, statement) into this->run_query(sql, arguments)',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
mysqli_query($db, $variable);
CODE_SAMPLE
        ,
            <<<'CODE_SAMPLE'
// @TODO: remove if covered by gateway-call
mysqli_query($db, $variable);
CODE_SAMPLE
        ),
            ]);
    }

    public function getNodeTypes(): array {
        return [Expression::class];
    }

    public function refactor(Node $node): ?Node {
        if (! $node instanceof Expression) {
            return null;
        }
        if (! $node->expr instanceof FuncCall) {
            return null;
        }
        $func_call = $node->expr;
        if (! $this->isName($func_call->name, 'mysqli_query')) {
            return null;
        }
        if (count($func_call->args) < 2) {
            return null;
        }
        // deze regel behandelt het geval met de query in een variabele
        if (! $func_call->args[1]->value instanceof Variable) {
            return null;
        }
        if ($this->isDefinedBefore($func_call->args[1]->value, $node)) {
            return null;
        }
        $node->setDocComment(new Doc('// @TODO: remove if covered by gateway-call'));
        return $node;

        return NodeTraverser::REMOVE_NODE;
        # de SplitSqlConcatenation doet meteen de mysqli-aanroep,
        #   dus we wissen deze.
        # %#@#&*%^#%&* DAT BREEKT SOMMIGE CODE.
        # Kunnen we nou echt niet uitzoeken of die variabele ergens voor ons gedeclareerd is?
    }

    private function isDefinedBefore($var, $node) {
        return false; // onduidelijk hoe dit zou moeten. Er is geen scope.
    }

}
